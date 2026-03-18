<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class SmsTestController extends Controller
{
    public function __construct(private readonly SmsService $smsService)
    {
    }

    public function index()
    {
        $providers = $this->smsService->providerStatus();

        return view('administration.sms_test', compact('providers'));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'number' => ['required', 'string', 'max:20', 'regex:/^\+?[1-9]\d{7,14}$/'],
            'message' => ['required', 'string', 'max:1000'],
            'provider' => ['required', 'in:infobip,twilio,zitasms'],
        ], [
            'number.regex' => 'Le numéro doit être au format international, par exemple +243991234567.',
        ]);

        try {
            $result = $this->smsService->send(
                $validated['provider'],
                $validated['number'],
                $validated['message'],
            );

            $successMessage = $result['mode'] === 'simulation'
                ? 'Test SMS simulé avec succès.'
                : 'SMS envoyé avec succès.';

            return redirect()
                ->route('administration.sms_test.index')
                ->with('success', $successMessage)
                ->with('sms_result', $result)
                ->withInput();
        } catch (Throwable $exception) {
            Log::warning('Echec du test SMS.', [
                'provider' => $validated['provider'],
                'number' => $validated['number'],
                'error' => $exception->getMessage(),
                'user_id' => $request->user()?->id,
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('administration.sms_test.index')
                ->with('error', $exception->getMessage())
                ->withInput();
        }
    }
}