@extends('impressions._layout')

@section('titre', 'Fiche Credit - ' . $demande->numero_dossier)

@section('contenu')

@php
    $client = optional($demande)->client;
    $clientFullName = trim(($client->nom ?? '') . ' ' . ($client->postnom ?? '') . ' ' . ($client->prenom ?? ''));
    $clientPhotoBase64 = null;

    if (!empty($client?->photo)) {
        $photoPath = base_path('images_projet/clients/' . basename($client->photo));
        if (file_exists($photoPath)) {
            $mime = mime_content_type($photoPath) ?: 'image/jpeg';
            $clientPhotoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($photoPath));
        }
    }
@endphp

<style>
    .pdf-keep {
        page-break-inside: avoid;
    }

    .pdf-keep table,
    .pdf-keep tr,
    .pdf-keep td,
    .pdf-keep th {
        page-break-inside: avoid;
    }
</style>

<div class="doc-title">Fiche complete du dossier de credit</div>

<div class="section pdf-keep">
    <div class="section-title">Identification dossier</div>
    <table class="info-table">
        <tr>
            <td class="label">Numero dossier</td>
            <td><strong>{{ $demande->numero_dossier }}</strong></td>
            <td class="label">Statut</td>
            <td>{{ $demande->statut }}</td>
        </tr>
        <tr>
            <td class="label">Date creation</td>
            <td>{{ optional($demande->created_at)->format('d/m/Y H:i') }}</td>
            <td class="label">Date soumission</td>
            <td>{{ optional($demande->date_soumission)->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Type credit</td>
            <td>{{ $demande->type_credit }}</td>
            <td class="label">Zone</td>
            <td>{{ $demande->code_zone ?? '-' }}</td>
        </tr>
    </table>
</div>

<div class="section pdf-keep">
    <div class="section-title">Client beneficiaire</div>
    <table class="info-table" style="margin-bottom:8px;">
        <tr>
            <td class="label">Photo client</td>
            <td>
                @if($clientPhotoBase64)
                    <img src="{{ $clientPhotoBase64 }}" alt="Photo client" style="width:90px;height:110px;object-fit:cover;border:1px solid #999;border-radius:6px;">
                @else
                    <span class="text-muted">Aucune photo</span>
                @endif
            </td>
            <td class="label">Demande créée par</td>
            <td>
                {{ $demandeurMeta['nom_complet'] ?? ($demande->agent_createur_matricule ?? '-') }}
                @if(!empty($demandeurMeta['role_nom']) || !empty($demandeurMeta['role_code']))
                    <br><span class="text-muted">Rôle : {{ $demandeurMeta['role_nom'] ?? $demandeurMeta['role_code'] }}</span>
                @endif
            </td>
        </tr>
    </table>
    <table class="info-table">
        <tr>
            <td class="label">Matricule</td>
            <td>{{ $demande->client_matricule }}</td>
            <td class="label">Nom complet</td>
            <td>{{ $clientFullName !== '' ? $clientFullName : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Telephone</td>
            <td>{{ optional($demande->client)->telephone ?? '-' }}</td>
            <td class="label">Adresse</td>
            <td>{{ optional($demande->client)->adresse ?? '-' }}</td>
        </tr>
    </table>
</div>

<div class="section pdf-keep">
    <div class="section-title">Parametres financiers</div>
    <table class="info-table">
        <tr>
            <td class="label">Montant demande</td>
            <td>{{ number_format($demande->montant_demande, 2, ',', ' ') }} {{ $demande->devise }}</td>
            <td class="label">Montant accorde</td>
            <td>{{ $demande->montant_accorde ? number_format($demande->montant_accorde, 2, ',', ' ') : '-' }} {{ $demande->devise }}</td>
        </tr>
        <tr>
            <td class="label">Duree</td>
            <td>{{ $demande->duree_mois }} mois</td>
            <td class="label">Taux mensuel</td>
            <td>{{ number_format($demande->taux_interet_mensuel, 2, ',', ' ') }} %</td>
        </tr>
        <tr>
            <td class="label">Frais dossier</td>
            <td>{{ number_format($demande->frais_dossier ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</td>
            <td class="label">Commission totale</td>
            <td>{{ number_format($demande->commission_totale ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</td>
        </tr>
        <tr>
            <td class="label">Capital restant</td>
            <td>{{ number_format($demande->capital_restant ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</td>
            <td class="label">Commission par échéance</td>
            <td>{{ $demande->duree_mois > 0 ? number_format(($demande->commission_totale ?? 0) / $demande->duree_mois, 2, ',', ' ') : '0,00' }} {{ $demande->devise }}</td>
        </tr>
    </table>
</div>

<div class="section pdf-keep">
    <div class="section-title">Objet et garanties</div>
    <table class="info-table">
        <tr>
            <td class="label">Objet du credit</td>
            <td>{{ $demande->objet_credit }}</td>
        </tr>
        <tr>
            <td class="label">Garanties</td>
            <td>{{ $demande->garantie_description ?? '-' }}</td>
        </tr>
        @if($demande->motif_rejet)
        <tr>
            <td class="label">Motif rejet</td>
            <td>{{ $demande->motif_rejet }}</td>
        </tr>
        @endif
        @if($demande->motif_annulation)
        <tr>
            <td class="label">Motif annulation</td>
            <td>{{ $demande->motif_annulation }}</td>
        </tr>
        @endif
    </table>
</div>

@if($demande->analyse)
<div style="page-break-before: always;"></div>
<div class="section pdf-keep">
    <div class="section-title">Analyse credit</div>
    <table class="info-table" style="font-size:10px;">
        <tr>
            <td class="label">Activite principale</td>
            <td>{{ $demande->analyse->activite_principale ?? '-' }}</td>
            <td class="label">Recommandation</td>
            <td>{{ $demande->analyse->recommandation ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Analysé par</td>
            <td>{{ optional($demande->analyse->analyseur)->nom_complet ?? ($demande->agent_analyse_matricule ?? '-') }}</td>
            <td class="label">Date analyse</td>
            <td>{{ optional($demande->analyse->date_analyse)->format('d/m/Y H:i') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Revenu mensuel net</td>
            <td>{{ number_format($demande->analyse->revenu_mensuel_net ?? 0, 2, ',', ' ') }}</td>
            <td class="label">Capacite remboursement</td>
            <td>{{ number_format($demande->analyse->capacite_remboursement ?? 0, 2, ',', ' ') }}</td>
        </tr>
        <tr>
            <td class="label">Taux endettement</td>
            <td>{{ number_format($demande->analyse->taux_endettement ?? 0, 2, ',', ' ') }} %</td>
            <td class="label">Score risque</td>
            <td>{{ $demande->analyse->score_risque ?? '-' }}/100</td>
        </tr>
        <tr>
            <td class="label">Observations</td>
            <td colspan="3">{{ $demande->analyse->observations ?? '-' }}</td>
        </tr>
    </table>
</div>
@endif

<div class="section pdf-keep">
    <div class="section-title">Validation multicouche</div>
    <table class="info-table" style="font-size:10px;">
        <thead>
            <tr style="background:#eef7f2;">
                <td class="label">Niveau</td>
                <td class="label">Decision</td>
                <td class="label">Date</td>
                <td class="label">Validateur</td>
                <td class="label">Commentaire</td>
            </tr>
        </thead>
        <tbody>
            @foreach($demande->validations as $v)
            <tr>
                <td>{{ str_replace('_',' ', $v->type_validateur) }}</td>
                <td>{{ str_replace('_',' ', $v->decision) }}</td>
                <td>{{ optional($v->date_validation)->format('d/m/Y H:i') ?? '-' }}</td>
                <td>{{ optional($v->validateur)->nom_complet ?? '-' }}</td>
                <td>{{ $v->commentaire ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="section pdf-keep">
    <div class="section-title">Pieces justificatives</div>
    <table class="info-table" style="font-size:10px;">
        <thead>
            <tr style="background:#eef7f2;">
                <td class="label">Type piece</td>
                <td class="label">Statut</td>
                <td class="label">Reference</td>
                <td class="label">Commentaire</td>
            </tr>
        </thead>
        <tbody>
            @foreach($demande->pieces as $p)
            <tr>
                <td>{{ $p->type_piece }}</td>
                <td>{{ $p->fourni ? 'Fourni' : 'Manquant' }}</td>
                <td>{{ $p->reference ?? '-' }}</td>
                <td>{{ $p->commentaire ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($demande->deblocages->count())
<div class="section pdf-keep">
    <div class="section-title">Deblocage et execution</div>
    @php $db = $demande->deblocages->first() @endphp
    <table class="info-table">
        <tr>
            <td class="label">Date deblocage</td>
            <td>{{ optional($db->date_deblocage)->format('d/m/Y') ?? '-' }}</td>
            <td class="label">Montant debloque</td>
            <td>{{ number_format($db->montant_debloque ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</td>
        </tr>
        <tr>
            <td class="label">Date 1er remboursement</td>
            <td>{{ optional($db->date_premier_remboursement)->format('d/m/Y') ?? '-' }}</td>
            <td class="label">Operateur</td>
            <td>{{ optional($db->operateur)->nom_complet ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Guichet debite</td>
            <td>{{ optional(optional($db->guichetSolde)->guichet)->intitule ?? optional($db->guichetSolde)->devise_code ?? '-' }}</td>
            <td class="label">Compte credit client</td>
            <td>{{ optional($db->compteCredit)->code_compte ?? '-' }}</td>
        </tr>
    </table>
</div>
@endif

@if($demande->echeancier)
<div class="section pdf-keep">
    <div class="section-title">Synthese echeancier</div>
    <table class="info-table" style="font-size:10px;">
        <tr>
            <td class="label">Nombre echeances</td>
            <td>{{ $demande->echeancier->echeances->count() }}</td>
            <td class="label">Total interets</td>
            <td>{{ number_format($demande->echeancier->total_interets ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</td>
        </tr>
        <tr>
            <td class="label">Montant total echeancier</td>
            <td>{{ number_format($demande->echeancier->montant_total ?? 0, 2, ',', ' ') }} {{ $demande->devise }}</td>
            <td class="label">Echeances en retard</td>
            <td>{{ $demande->echeancier->echeances->where('statut', 'EN_RETARD')->count() }}</td>
        </tr>
    </table>
</div>
@endif

<script type="text/php">
if (isset($pdf)) {
    $pdf->page_text(510, 816, 'Page {PAGE_NUM}/{PAGE_COUNT}', null, 8, [0.35, 0.35, 0.35]);
}
</script>

@endsection
