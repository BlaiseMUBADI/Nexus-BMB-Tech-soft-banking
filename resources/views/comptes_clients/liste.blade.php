@extends('layouts.app')

@section('page_title', 'Liste des comptes')
@section('breadcrumb_parent', 'Gestion des comptes')
@section('breadcrumb', 'Liste des comptes')

@push('css')
<style>
    /* ── Context Menu ─────────────────────────────── */
    #contextMenuCompte {
        display: none;
        position: absolute;
        z-index: 9999;
        background: #2d3748;
        box-shadow: 0 8px 32px rgba(0,0,0,.45);
        border-radius: 12px;
        min-width: 270px;
        border: 1px solid #4a5568;
        overflow: hidden;
    }
    #contextMenuCompte .ctx-header {
        padding: 13px 18px;
        font-weight: 600;
        font-size: .97em;
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
        letter-spacing: .4px;
    }
    #contextMenuCompte .ctx-divider {
        border-bottom: 1px solid #4a5568;
        margin: 4px 12px;
    }
    #contextMenuCompte .ctx-section { padding: 8px 8px 10px; }
    #contextMenuCompte ul { list-style: none; margin: 0; padding: 0; }
    #contextMenuCompte ul li { margin-bottom: 2px; }
    #contextMenuCompte a.ctx-item {
        display: flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 7px;
        color: #cbd5e0;
        font-size: .93em;
        text-decoration: none;
        transition: background .15s, color .15s;
    }
    #contextMenuCompte a.ctx-item:hover { background: #3b82f6; color: #fff; }
    #contextMenuCompte a.ctx-item i { margin-right: 9px; width: 16px; text-align: center; opacity: .85; }
    #contextMenuCompte a.ctx-item.ctx-danger:hover { background: #e53e3e; color: #fff; }
    #comptesTable tbody tr:hover { cursor: context-menu; }
    #comptesTable tbody tr.ctx-active { background: rgba(59,130,246,.12) !important; }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ── Mini-dashboard ──────────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-university"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total comptes</span>
                    <span class="info-box-number">{{ $stats['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-exchange-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Courant</span>
                    <span class="info-box-number">{{ $stats['courant'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-piggy-bank"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Épargne</span>
                    <span class="info-box-number">{{ $stats['epargne'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hand-holding-usd"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Caution / Crédit</span>
                    <span class="info-box-number">{{ $stats['caution_credit'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main card ──────────────────────────────────────── --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Comptes clients</h3>
            <div class="card-tools d-flex align-items-center">
                <div class="input-group input-group-sm mr-2" style="width:220px;">
                    <input type="text" id="searchComptes" class="form-control" placeholder="Rechercher…">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <a href="{{ route('comptes.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle mr-1"></i> Ouvrir un compte
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0" id="comptesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Code Compte</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Solde réel</th>
                            <th>Devise</th>
                            <th>Portefeuille</th>
                            <th style="width:120px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comptes as $loopIndex => $compte)
                        <tr data-code="{{ $compte->code_compte }}">
                            <td>{{ $loopIndex + 1 }}</td>
                            <td><code>{{ $compte->code_compte }}</code></td>
                            <td>
                                @if($compte->client)
                                    {{ $compte->client->nom }}
                                    {{ $compte->client->postnom ?? '' }}
                                    {{ $compte->client->prenom ?? '' }}
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeBadge = [
                                        'COURANT'         => 'badge-info',
                                        'EPARGNE_LIBRE'   => 'badge-success',
                                        'EPARGNE_BLOQUEE' => 'badge-warning',
                                        'CAUTION_CREDIT'  => 'badge-primary',
                                    ][$compte->type] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $typeBadge }}">{{ $compte->type }}</span>
                            </td>
                            <td class="text-right">{{ number_format($compte->solde_reel, 2, ',', ' ') }}</td>
                            <td><span class="badge badge-secondary">{{ $compte->devise }}</span></td>
                            <td>
                                @if($compte->portefeuille_id && $compte->portefeuille && $compte->portefeuille->agent)
                                    <small>({{ $compte->portefeuille->agent->matricule }})
                                    {{ $compte->portefeuille->agent->nom }}
                                    {{ $compte->portefeuille->agent->prenom }}</small>
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('comptes.show', $compte->code_compte) }}"
                                   class="btn btn-xs btn-info mr-1" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('comptes.edit', $compte->code_compte) }}"
                                   class="btn btn-xs btn-warning mr-1" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-xs btn-danger btn-delete-compte"
                                        data-id="{{ $compte->code_compte }}"
                                        data-url="{{ route('comptes.destroy', $compte->code_compte) }}"
                                        title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucun compte enregistré.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── Context menu (clic droit) ─────────────────────── --}}
<div id="contextMenuCompte">
    <div class="ctx-header"><i class="fas fa-university mr-2"></i>Actions sur le compte</div>
    <div class="ctx-section">
        <ul>
            <li><a href="#" class="ctx-item" id="ctxVoir"><i class="fas fa-eye"></i> Voir le compte</a></li>
            <li><a href="#" class="ctx-item" id="ctxEdit"><i class="fas fa-edit"></i> Modifier</a></li>
            <li><a href="#" class="ctx-item ctx-danger" id="ctxDelete"><i class="fas fa-trash-alt"></i> Supprimer</a></li>
        </ul>
        <div class="ctx-divider"></div>
        <ul>
            <li><a href="#" class="ctx-item"><i class="fas fa-file-alt"></i> Imprimer RIB / IBAN</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-file-contract"></i> Convention de compte</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-certificate"></i> Certificat d'ouverture</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-list-alt"></i> Relevé de compte</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-receipt"></i> Avis d'opération</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-chart-line"></i> Échelle d'intérêts</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-table"></i> Tableau d'amortissement</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-balance-scale"></i> Attestation de solde</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-check-circle"></i> Attestation de non-redevance</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-id-card"></i> Fiche client (KYC)</a></li>
            <li><a href="#" class="ctx-item"><i class="fas fa-pen-nib"></i> Spécimen de signature</a></li>
        </ul>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    'use strict';
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    var showUrl   = "{{ route('comptes.show',   '__ID__') }}";
    var editUrl   = "{{ route('comptes.edit',   '__ID__') }}";
    var deleteUrl = "{{ route('comptes.destroy','__ID__') }}";

    $(function () {

        /* ── Live search ─────────────────────────────── */
        $('#searchComptes').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#comptesTable tbody tr').each(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
            });
        });

        /* ── Context-menu (right-click) ──────────────── */
        var $ctxMenu = $('#contextMenuCompte');
        var ctxCode  = null;

        $('#comptesTable tbody').on('contextmenu', 'tr', function (e) {
            e.preventDefault();
            ctxCode = $(this).data('code');
            $(this).addClass('ctx-active').siblings().removeClass('ctx-active');
            $ctxMenu.css({ left: e.pageX + 'px', top: e.pageY + 'px' }).show();
            $(document).one('click.ctxCompte', function () {
                $ctxMenu.hide();
                $('#comptesTable tbody tr').removeClass('ctx-active');
            });
        });

        $('#ctxVoir').on('click', function (e) {
            e.preventDefault();
            if (ctxCode) window.location.href = showUrl.replace('__ID__', ctxCode);
        });
        $('#ctxEdit').on('click', function (e) {
            e.preventDefault();
            if (ctxCode) window.location.href = editUrl.replace('__ID__', ctxCode);
        });
        $('#ctxDelete').on('click', function (e) {
            e.preventDefault();
            $ctxMenu.hide();
            if (ctxCode) triggerDelete(ctxCode, deleteUrl.replace('__ID__', ctxCode));
        });

        /* ── Bouton supprimer inline ─────────────────── */
        $(document).on('click', '.btn-delete-compte', function () {
            triggerDelete($(this).data('id'), $(this).data('url'));
        });

        function triggerDelete(code, url) {
            showUniversalConfirm(
                'Voulez-vous vraiment supprimer le compte <strong>' + code + '</strong> ?',
                function () {
                    $.post(url, { _method: 'DELETE' })
                        .done(function (res) {
                            showSystemMessage('success', res.message || 'Compte supprimé avec succès.');
                            setTimeout(function () { window.location.reload(); }, 1200);
                        })
                        .fail(function (xhr) {
                            var msg = 'Erreur lors de la suppression.';
                            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                            showSystemMessage('error', msg);
                        });
                },
                'Confirmation suppression'
            );
        }

    });
}());
</script>
@endpush
