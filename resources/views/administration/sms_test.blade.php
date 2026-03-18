@extends('layouts.app')

@section('page_title', 'Test SMS')
@section('breadcrumb_parent', 'Administration')
@section('breadcrumb', 'Test SMS')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card card-primary card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Infobip, Twilio et ZitaSMS, c'est quoi ?</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Infobip</strong>, <strong>Twilio</strong> et <strong>ZitaSMS</strong> sont des plateformes d'envoi SMS via API.</p>
                    <p class="mb-2">Le principe est simple : votre application envoie une requête HTTP au fournisseur, qui se charge ensuite d'acheminer le SMS vers le numéro du destinataire.</p>
                    <p class="mb-0">Cette page vous permet de faire un petit test en choisissant le fournisseur, le numéro et le message à envoyer.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info card-outline h-100">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-ol mr-2"></i>Étapes de test</h3>
                </div>
                <div class="card-body">
                    <ol class="mb-0 pl-3">
                        <li>Laissez d'abord <strong>SMS_SIMULATE=true</strong> pour vérifier le formulaire sans envoyer de vrai SMS.</li>
                        <li>Renseignez ensuite les clés API dans le fichier <code>.env</code>.</li>
                        <li>Passez <strong>SMS_SIMULATE=false</strong> quand vous êtes prêt pour un test réel.</li>
                        <li>Saisissez le numéro au format international, par exemple <strong>+243991234567</strong>.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        @foreach($providers as $key => $provider)
            <div class="col-md-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon {{ $provider['configured'] ? 'bg-success' : 'bg-warning' }} elevation-1">
                        <i class="fas {{ $key === 'infobip' ? 'fa-broadcast-tower' : ($key === 'twilio' ? 'fa-phone-volume' : 'fa-sim-card') }}"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ $provider['label'] }}</span>
                        <span class="info-box-number" style="font-size:1rem;">
                            {{ $provider['simulate'] ? 'Mode simulation' : 'Mode réel' }}
                        </span>
                        <span class="text-muted">
                            {{ $provider['configured'] ? 'Configuration détectée' : 'Configuration incomplète' }}
                            @if($provider['from'])
                                · Expéditeur : {{ $provider['from'] }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-paper-plane mr-2"></i>Envoyer un SMS de test</h3>
                </div>
                <form method="POST" action="{{ route('administration.sms_test.send') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="number">Numéro du destinataire</label>
                            <input
                                type="text"
                                name="number"
                                id="number"
                                class="form-control @error('number') is-invalid @enderror"
                                value="{{ old('number') }}"
                                placeholder="Ex : +243991234567"
                                required
                            >
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea
                                name="message"
                                id="message"
                                rows="5"
                                class="form-control @error('message') is-invalid @enderror"
                                maxlength="1000"
                                required
                            >{{ old('message', 'Bonjour, ceci est un SMS de test depuis Coopec EBEN.') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="d-block">Fournisseur</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input" type="radio" id="provider_infobip" name="provider" value="infobip" {{ old('provider', 'infobip') === 'infobip' ? 'checked' : '' }}>
                                <label for="provider_infobip" class="custom-control-label">Infobip</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input" type="radio" id="provider_twilio" name="provider" value="twilio" {{ old('provider') === 'twilio' ? 'checked' : '' }}>
                                <label for="provider_twilio" class="custom-control-label">Twilio</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input" type="radio" id="provider_zitasms" name="provider" value="zitasms" {{ old('provider') === 'zitasms' ? 'checked' : '' }}>
                                <label for="provider_zitasms" class="custom-control-label">ZitaSMS</label>
                            </div>
                            @error('provider')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="text-muted">Le test utilise Infobip, Twilio ou ZitaSMS selon votre choix.</small>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane mr-1"></i> Envoyer le test
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-terminal mr-2"></i>Dernier résultat</h3>
                </div>
                <div class="card-body">
                    @if(session('sms_result'))
                        <dl class="row mb-3">
                            <dt class="col-sm-5">Fournisseur</dt>
                            <dd class="col-sm-7 text-capitalize">{{ data_get(session('sms_result'), 'provider') }}</dd>

                            <dt class="col-sm-5">Mode</dt>
                            <dd class="col-sm-7 text-capitalize">{{ data_get(session('sms_result'), 'mode') }}</dd>

                            <dt class="col-sm-5">Statut HTTP</dt>
                            <dd class="col-sm-7">{{ data_get(session('sms_result'), 'status') }}</dd>

                            <dt class="col-sm-5">Référence</dt>
                            <dd class="col-sm-7">{{ data_get(session('sms_result'), 'reference', '—') }}</dd>
                        </dl>

                        <div class="bg-dark rounded p-2" style="max-height:320px; overflow:auto;">
                            <pre class="mb-0 text-white" style="font-size:.8rem;">{{ json_encode(data_get(session('sms_result'), 'raw'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucun test n'a encore été exécuté depuis cette session.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection