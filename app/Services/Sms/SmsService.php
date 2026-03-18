<?php

namespace App\Services\Sms;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class SmsService
{
    public function providerStatus(): array
    {
        return [
            'infobip' => [
                'label' => 'Infobip',
                'configured' => filled(config('services.infobip.base_url'))
                    && filled(config('services.infobip.api_key'))
                    && filled(config('services.infobip.from')),
                'simulate' => $this->simulationEnabled('infobip'),
                'from' => config('services.infobip.from'),
            ],
            'twilio' => [
                'label' => 'Twilio',
                'configured' => filled(config('services.twilio.account_sid'))
                    && filled(config('services.twilio.auth_token'))
                    && filled(config('services.twilio.from')),
                'simulate' => $this->simulationEnabled('twilio'),
                'from' => config('services.twilio.from'),
            ],
            'zitasms' => [
                'label' => 'ZitaSMS',
                'configured' => filled(config('services.zitasms.base_url'))
                    && filled(config('services.zitasms.api_key')),
                'simulate' => $this->simulationEnabled('zitasms'),
                'from' => 'Device ' . (string) config('services.zitasms.device', 0),
            ],
        ];
    }

    public function send(string $provider, string $number, string $message): array
    {
        return match ($provider) {
            'infobip' => $this->sendInfobip($number, $message),
            'twilio' => $this->sendTwilio($number, $message),
            'zitasms' => $this->sendZitaSms($number, $message),
            default => throw new InvalidArgumentException('Fournisseur SMS non supporté.'),
        };
    }

    protected function sendZitaSms(string $number, string $message): array
    {
        $baseUrl = $this->normalizeBaseUrl((string) config('services.zitasms.base_url'));
        $apiKey = trim((string) config('services.zitasms.api_key'));
        $device = (string) config('services.zitasms.device', 0);

        $payload = [
            'number' => $number,
            'message' => $message,
            'schedule' => null,
            'key' => $apiKey,
            'devices' => $device,
            'type' => 'sms',
            'attachments' => null,
            'prioritize' => 0,
        ];

        if ($this->simulationEnabled('zitasms')) {
            Log::info('SMS test ZitaSMS simulé.', [
                'number' => $number,
                'message_preview' => Str::limit($message, 160),
                'payload' => $payload,
            ]);

            return [
                'provider' => 'zitasms',
                'mode' => 'simulation',
                'status' => 200,
                'reference' => 'simulated-zitasms',
                'raw' => [
                    'message' => 'Envoi simulé. Activez la configuration ZitaSMS pour un test réel.',
                    'payload' => $payload,
                ],
            ];
        }

        if ($baseUrl === '' || $apiKey === '') {
            throw new RuntimeException('ZitaSMS non configuré. Renseignez ZITASMS_BASE_URL et ZITASMS_API_KEY dans le fichier .env.');
        }

        /** @var Response $response */
        $response = $this->httpClient()
            ->asForm()
            ->post($baseUrl . '/services/send.php', $payload);

        if ($response->failed()) {
            throw new RuntimeException($this->buildHttpErrorMessage('ZitaSMS', $response->status(), $response->body()));
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('ZitaSMS a retourné une réponse invalide (JSON attendu).');
        }

        if ((bool) data_get($json, 'success') !== true) {
            throw new RuntimeException('ZitaSMS a rejeté la requête: ' . (string) data_get($json, 'error.message', 'erreur inconnue'));
        }

        $firstMessage = data_get($json, 'data.messages.0');

        return [
            'provider' => 'zitasms',
            'mode' => 'live',
            'status' => $response->status(),
            'reference' => data_get($firstMessage, 'ID', data_get($firstMessage, 'groupID')),
            'raw' => $json,
        ];
    }

    protected function sendInfobip(string $number, string $message): array
    {
        $payload = [
            'messages' => [[
                'from' => config('services.infobip.from'),
                'destinations' => [
                    ['to' => $number],
                ],
                'text' => $message,
            ]],
        ];

        if ($this->simulationEnabled('infobip')) {
            Log::info('SMS test Infobip simulé.', [
                'number' => $number,
                'message_preview' => Str::limit($message, 160),
                'payload' => $payload,
            ]);

            return [
                'provider' => 'infobip',
                'mode' => 'simulation',
                'status' => 200,
                'reference' => 'simulated-infobip',
                'raw' => [
                    'message' => 'Envoi simulé. Activez la configuration Infobip pour un test réel.',
                    'payload' => $payload,
                ],
            ];
        }

        $baseUrl = $this->normalizeBaseUrl((string) config('services.infobip.base_url'));
        $apiKey = (string) config('services.infobip.api_key');
        $from = (string) config('services.infobip.from');

        if ($baseUrl === '' || $apiKey === '' || $from === '') {
            throw new RuntimeException('Infobip non configuré. Renseignez INFOBIP_BASE_URL, INFOBIP_API_KEY et INFOBIP_FROM dans le fichier .env.');
        }

        /** @var Response $response */
        $response = $this->httpClient()->withHeaders([
            'Authorization' => 'App ' . $apiKey,
            'Accept' => 'application/json',
        ])->post($baseUrl . '/sms/2/text/advanced', $payload);

        if ($response->failed()) {
            throw new RuntimeException($this->buildHttpErrorMessage('Infobip', $response->status(), $response->body()));
        }

        $sendJson = $response->json();
        $messageId = (string) data_get($sendJson, 'messages.0.messageId', '');
        $latestReport = $messageId !== ''
            ? $this->fetchInfobipLatestReport($baseUrl, $apiKey, $messageId)
            : null;

        return [
            'provider' => 'infobip',
            'mode' => 'live',
            'status' => $response->status(),
            'reference' => $messageId !== '' ? $messageId : null,
            'raw' => [
                'send_response' => $sendJson,
                'latest_report' => $latestReport,
            ],
        ];
    }

    protected function sendTwilio(string $number, string $message): array
    {
        $payload = [
            'To' => $number,
            'From' => config('services.twilio.from'),
            'Body' => $message,
        ];

        if ($this->simulationEnabled('twilio')) {
            Log::info('SMS test Twilio simulé.', [
                'number' => $number,
                'message_preview' => Str::limit($message, 160),
                'payload' => $payload,
            ]);

            return [
                'provider' => 'twilio',
                'mode' => 'simulation',
                'status' => 200,
                'reference' => 'simulated-twilio',
                'raw' => [
                    'message' => 'Envoi simulé. Activez la configuration Twilio pour un test réel.',
                    'payload' => $payload,
                ],
            ];
        }

        $accountSid = (string) config('services.twilio.account_sid');
        $authToken = (string) config('services.twilio.auth_token');
        $from = (string) config('services.twilio.from');

        if ($accountSid === '' || $authToken === '' || $from === '') {
            throw new RuntimeException('Twilio non configuré. Renseignez TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN et TWILIO_FROM dans le fichier .env.');
        }

        /** @var Response $response */
        $response = $this->httpClient()->asForm()
            ->withBasicAuth($accountSid, $authToken)
            ->post('https://api.twilio.com/2010-04-01/Accounts/' . $accountSid . '/Messages.json', $payload);

        if ($response->failed()) {
            throw new RuntimeException($this->buildHttpErrorMessage('Twilio', $response->status(), $response->body()));
        }

        return [
            'provider' => 'twilio',
            'mode' => 'live',
            'status' => $response->status(),
            'reference' => data_get($response->json(), 'sid'),
            'raw' => $response->json(),
        ];
    }

    protected function simulationEnabled(string $provider): bool
    {
        return (bool) config('services.sms.simulate', true)
            || (bool) config('services.' . $provider . '.simulate', false);
    }

    protected function buildHttpErrorMessage(string $provider, ?int $status, ?string $body): string
    {
        if ($body) {
            return $provider . ' a rejeté la requête (' . $status . ') : ' . $body;
        }

        return 'Erreur HTTP ' . $provider . ' (' . ($status ?? 'inconnu') . ').';
    }

    protected function normalizeBaseUrl(string $baseUrl): string
    {
        $baseUrl = trim($baseUrl);

        if ($baseUrl !== '' && !str_starts_with($baseUrl, 'http://') && !str_starts_with($baseUrl, 'https://')) {
            $baseUrl = 'https://' . $baseUrl;
        }

        return rtrim($baseUrl, '/');
    }

    protected function httpClient(): PendingRequest
    {
        $verifySsl = (bool) config('services.sms.verify_ssl', true);
        $caBundle = trim((string) config('services.sms.ca_bundle', ''));

        if (!$verifySsl) {
            return Http::withOptions(['verify' => false]);
        }

        if ($caBundle !== '') {
            return Http::withOptions(['verify' => $caBundle]);
        }

        return Http::withOptions([]);
    }

    protected function fetchInfobipLatestReport(string $baseUrl, string $apiKey, string $messageId): ?array
    {
        try {
            /** @var Response $reportResponse */
            $reportResponse = $this->httpClient()
                ->withHeaders([
                    'Authorization' => 'App ' . $apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($baseUrl . '/sms/1/reports', [
                    'messageId' => $messageId,
                    'limit' => 1,
                ]);

            if ($reportResponse->failed()) {
                return null;
            }

            return data_get($reportResponse->json(), 'results.0');
        } catch (\Throwable) {
            return null;
        }
    }
}