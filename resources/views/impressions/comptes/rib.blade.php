@extends('impressions._layout')

@section('titre', 'Relevé d\'Identité Bancaire (RIB)')

@section('contenu')

{{-- ── IBAN / BIC central ── --}}
<div class="iban-box">
    <div class="iban-label">IBAN</div>
    <div class="iban-value">{{ $iban }}</div>
    <div class="bic-value">Contact : <strong>(+243) 995977523 / 852924454</strong></div>
</div>

{{-- ── 2 colonnes : Titulaire + Compte ── --}}
<div class="two-col">

    {{-- Colonne gauche : Titulaire --}}
    <div class="col">
        <div class="section">
            <div class="section-title">Titulaire du compte</div>
            <table class="info-table">
                <tr>
                    <td class="label">Nom complet</td>
                    <td>
                        {{ strtoupper($client->nom) }}
                        {{ strtoupper($client->postnom ?? '') }}
                        {{ ucfirst(strtolower($client->prenom ?? '')) }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Matricule client</td>
                    <td>{{ $client->matricule }}</td>
                </tr>
                <tr>
                    <td class="label">Adresse</td>
                    <td>{{ $client->adresse ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Téléphone</td>
                    <td>{{ $client->telephone ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td>{{ $client->email ?? '—' }}</td>
                </tr>
                @if($client->type_piece_identite)
                <tr>
                    <td class="label">Pièce d'identité</td>
                    <td>{{ $client->type_piece_identite }} n° {{ $client->numero_piece_identite ?? '—' }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Colonne droite : Coordonnées bancaires --}}
    <div class="col">
        <div class="section">
            <div class="section-title">Coordonnées bancaires</div>
            <table class="info-table">
                <tr>
                    <td class="label">Code compte</td>
                    <td><strong>{{ $compte->code_compte }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Type de compte</td>
                    <td>
                        @php
                            $types = [
                                'CC'  => 'Compte Courant',
                                'RMB' => 'Remboursement',
                                'GTC' => 'Caution',
                                'DAT' => 'Dépôt à Terme',
                                'EAV' => 'Épargne & Vie',
                            ];
                        @endphp
                        {{ $types[$compte->type] ?? $compte->type }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Devise</td>
                    <td>{{ $devise->nom ?? $compte->devise }} ({{ $devise->symbole ?? $compte->devise }})</td>
                </tr>
                <tr>
                    <td class="label">Banque</td>
                    <td>Coopérative d'Épargne et de Crédit EBEN (COOPEC EBEN)</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td>contact@coopeceben.com</td>
                </tr>
                <tr>
                    <td class="label">Date d'ouverture</td>
                    <td>{{ \Carbon\Carbon::parse($compte->created_at)->format('d/m/Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

</div>

{{-- ── Note d'utilisation ── --}}
<div class="section" style="margin-top:6px;">
    <div class="section-title">Comment utiliser ce RIB ?</div>
    <table class="info-table">
        <tr>
            <td style="padding:5px 7px; color:#444; line-height:1.25; font-size:8.5px;">
                Ce Relevé d'Identité Bancaire vous permet de :
                <br>
                &bull; Communiquer vos coordonnées bancaires pour <strong>recevoir des virements</strong><br>
                &bull; Domicilier votre <strong>salaire</strong> ou vos <strong>revenus</strong><br>
                &bull; Mettre en place des <strong>prélèvements automatiques</strong> (eau, électricité, loyer…)<br>
                &bull; Effectuer des opérations <strong>interbancaires</strong>
            </td>
        </tr>
    </table>
</div>

{{-- Espace pour signature --}}
<div style="margin-top: 16px; display:table; width:100%;">
    <div style="display:table-cell; text-align:center; width:50%;">
        <div style="border-top:1px solid #999; width:120px; margin:0 auto; padding-top:3px; font-size:8px; color:#666;">
            Signature du titulaire
        </div>
    </div>
    <div style="display:table-cell; text-align:center; width:50%;">
        <div style="border-top:1px solid #999; width:120px; margin:0 auto; padding-top:3px; font-size:8px; color:#666;">
            Cachet &amp; Signature COOPEC EBEN
        </div>
    </div>
</div>

@endsection
