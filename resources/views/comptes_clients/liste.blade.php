@extends('layouts.app')

@section('page_title', 'Liste des comptes' . (($zoneRestriction['active'] ?? false) && !empty($zoneRestriction['zone_label']) ? ' (' . $zoneRestriction['zone_label'] . ')' : ''))
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

    {{-- ── Mini-dashboard (6 info-box : Total + 5 types) ── --}}
    <div class="row mb-3">
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-university"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total comptes</span>
                    <span class="info-box-number">{{ $stats['total'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-exchange-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Courant (CC)</span>
                    <span class="info-box-number">{{ $stats['courant'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-undo-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rembours. (RMB)</span>
                    <span class="info-box-number">{{ $stats['remboursement'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-shield-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Caution (GTC)</span>
                    <span class="info-box-number">{{ $stats['caution'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Dépôt Terme (DAT)</span>
                    <span class="info-box-number">{{ $stats['depot_terme'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6">
            <div class="info-box shadow-sm mb-2">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-piggy-bank"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Épargne (EAV)</span>
                    <span class="info-box-number">{{ $stats['epargne_vie'] }}</span>
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
                @if($canPrintDocuments ?? true)
                    <button type="button" class="btn btn-secondary btn-sm mr-2"
                            data-toggle="modal" data-target="#modalImpressionComptes"
                            title="Imprimer la liste">
                        <i class="fas fa-print mr-1"></i> Imprimer
                    </button>
                @endif
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
                        @php($pfAgent = $compte->portefeuille?->affectationActive?->agent ?? $compte->portefeuille?->agent)
                        <tr data-code="{{ $compte->code_compte }}"
                            data-search="{{ strtolower(trim($compte->code_compte . ' ' . ($compte->client?->full_name ?? '') . ' ' . $compte->type . ' ' . $compte->devise . ' ' . ($pfAgent?->full_name ?? '') . ' ' . ($pfAgent?->matricule ?? ''))) }}">
                            <td>{{ $loopIndex + 1 }}</td>
                            <td><code>{{ $compte->code_compte }}</code></td>
                            <td>
                                @if($compte->client)
                                    {{ $compte->client->full_name }}
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeLabels = [
                                        'CC'  => ['label' => 'Compte Courant',   'badge' => 'badge-info'],
                                        'RMB' => ['label' => 'Remboursement',     'badge' => 'badge-secondary'],
                                        'GTC' => ['label' => 'Caution',           'badge' => 'badge-primary'],
                                        'DAT' => ['label' => 'Dépôt à Terme',     'badge' => 'badge-warning'],
                                        'EAV' => ['label' => 'Épargne & Vie',     'badge' => 'badge-success'],
                                    ];
                                    $typeBadge = $typeLabels[$compte->type]['badge'] ?? 'badge-secondary';
                                    $typeLabel = $typeLabels[$compte->type]['label'] ?? $compte->type;
                                @endphp
                                <span class="badge {{ $typeBadge }}">{{ $compte->type }} - {{ $typeLabel }}</span>
                            </td>
                            <td class="text-right">{{ number_format($compte->solde_reel, 2, ',', ' ') }}</td>
                            <td><span class="badge badge-secondary">{{ $compte->devise }}</span></td>
                            <td>
                                @if($compte->portefeuille_id && $pfAgent)
                                    <small>({{ $pfAgent->matricule }})
                                    {{ $pfAgent->nom }}
                                    {{ $pfAgent->prenom }}</small>
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
                                @if($canPrintDocuments ?? true)
                                    <a href="{{ route('comptes.rib', $compte->code_compte) }}"
                                       target="_blank" class="btn btn-xs btn-secondary mr-1" title="RIB PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
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

{{-- ===== Modal Impression Liste Comptes ===== --}}
@if($canPrintDocuments ?? true)
    <div class="modal fade" id="modalImpressionComptes" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white py-2">
                    <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression — Comptes</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="formImpressionComptes" action="{{ route('comptes.liste.pdf') }}" method="GET" target="_blank">
                    <div class="modal-body">
                        <div class="row">
                            {{-- Type de compte --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Type de compte</label>
                                    <select name="type" class="form-control form-control-sm">
                                        <option value="tous">— Tous les types —</option>
                                        <option value="CC">Compte Courant (CC)</option>
                                        <option value="RMB">Remboursement (RMB)</option>
                                        <option value="GTC">Caution (GTC)</option>
                                        <option value="DAT">Dépôt à Terme (DAT)</option>
                                        <option value="EAV">Épargne & Vie (EAV)</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Devise --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Devise</label>
                                    <select name="devise" class="form-control form-control-sm">
                                        <option value="tous">— Toutes les devises —</option>
                                        @foreach($devises as $d)
                                            <option value="{{ $d->code_iso }}">{{ $d->nom }} ({{ $d->code_iso }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- Zone du client --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Zone du titulaire</label>
                                    <select name="code_zone" class="form-control form-control-sm">
                                        <option value="">— Toutes les zones —</option>
                                        @foreach($zones as $z)
                                            <option value="{{ $z->code_zone }}">{{ $z->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- État du solde --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">État du solde réel</label>
                                    <select name="etat_solde" class="form-control form-control-sm">
                                        <option value="tous">— Tous —</option>
                                        <option value="positif">Solde positif (&gt; 0)</option>
                                        <option value="nul">Solde nul (= 0)</option>
                                        <option value="negatif">Solde négatif (&lt; 0)</option>
                                    </select>
                                </div>
                            </div>
                            {{-- Plage de dates d'ouverture --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Ouvert à partir du</label>
                                    <input type="date" name="date_debut" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Ouvert jusqu'au</label>
                                    <input type="date" name="date_fin" class="form-control form-control-sm">
                                </div>
                            </div>
                            {{-- Plage de solde --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Solde réel minimum</label>
                                    <input type="number" name="solde_min" step="0.01" placeholder="ex: 0"
                                           class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Solde réel maximum</label>
                                    <input type="number" name="solde_max" step="0.01" placeholder="ex: 100000"
                                           class="form-control form-control-sm">
                                </div>
                            </div>
                            {{-- Portefeuille --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="font-weight-bold small">Portefeuille (Agent commercial)</label>
                                    <select name="portefeuille_id" class="form-control form-control-sm">
                                        <option value="tous">— Tous les portefeuilles —</option>
                                        <option value="aucun">Sans portefeuille assigné</option>
                                        @foreach($portefeuilles as $p)
                                            @php($pfAgent = $p->affectationActive->agent ?? $p->agent)
                                            <option value="{{ $p->id }}">
                                                {{ $p->nom_portefeuille }}
                                                @if($pfAgent)
                                                        &mdash; {{ $pfAgent->full_name }}
                                                    ({{ $pfAgent->matricule }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info py-1 small mb-0">
                            <i class="fas fa-info-circle mr-1"></i>
                            Laissez vide pour ne pas filtrer. Le PDF s'ouvre en paysage dans un nouvel onglet.
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-file-pdf mr-1"></i> Générer le PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

{{-- ── Context menu (clic droit) ─────────────────────── --}}
<div id="contextMenuCompte">
    <div class="ctx-header"><i class="fas fa-university mr-2"></i>Actions sur le compte</div>
    <div class="ctx-section">
        <ul>
            @if($canPrintDocuments ?? true)
                <li><a href="#" class="ctx-item" id="ctxRIB"><i class="fas fa-file-alt"></i> Imprimer RIB / IBAN</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-file-contract"></i> Convention de compte</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-certificate"></i> Certificat d'ouverture</a></li>
                <li><a href="#" class="ctx-item" id="ctxReleve"><i class="fas fa-list-alt text-success"></i> Relevé bancaire</a></li>
            @endif
            <li><a href="#" class="ctx-item" id="ctxHistorique"><i class="fas fa-history text-primary"></i> Historique des mouvements</a></li>
            @if($canPrintDocuments ?? true)
                <li><a href="#" class="ctx-item"><i class="fas fa-receipt"></i> Avis d'opération</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-chart-line"></i> Échelle d'intérêts</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-table"></i> Tableau d'amortissement</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-balance-scale"></i> Attestation de solde</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-check-circle"></i> Attestation de non-redevance</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-id-card"></i> Fiche client (KYC)</a></li>
                <li><a href="#" class="ctx-item"><i class="fas fa-pen-nib"></i> Spécimen de signature</a></li>
            @endif
        </ul>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL — Paramètres relevé bancaire
     ══════════════════════════════════════════════════════════════ --}}
@if($canPrintDocuments ?? true)
    <div class="modal fade" id="modalReleve" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content shadow" style="border-radius:12px;overflow:hidden;">
                <div class="modal-header py-2" style="background:linear-gradient(90deg,#059669 0%,#047857 100%);">
                    <h6 class="modal-title text-white mb-0">
                        <i class="fas fa-list-alt mr-2"></i>Relevé bancaire
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-2">
                        <i class="fas fa-university mr-1 text-success"></i>
                        Compte&nbsp;: <strong id="releveCompteCode">—</strong>
                    </p>
                    <div class="form-group mb-2">
                        <label class="small font-weight-bold">Période du</label>
                        <input type="date" id="releveDebut" class="form-control form-control-sm"
                               value="{{ now()->startOfMonth()->toDateString() }}">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">Au</label>
                        <input type="date" id="releveFin" class="form-control form-control-sm"
                               value="{{ now()->toDateString() }}">
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-sm btn-success" id="btnGenererReleve">
                        <i class="fas fa-file-pdf mr-1"></i>Générer PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection

@push('js')
<script>
(function () {
    'use strict';
    $.ajaxSetup({ headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept'      : 'application/json'
    } });

    $(function () {

        /* ── Live search ─────────────────────────────── */
        $('#searchComptes').on('input', function () {
            var q = $(this).val().toLowerCase();
            $('#comptesTable tbody tr').each(function () {
                var haystack = ($(this).data('search') || $(this).text()).toString().toLowerCase();
                $(this).toggle(haystack.indexOf(q) !== -1);
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

        /* ── Liens menu contextuel ───────────────────── */
        var ribUrl        = "{{ route('comptes.rib', ['code_compte' => '__ID__']) }}";
        var releveUrl     = "{{ route('comptes.releve.pdf', ['code_compte' => '__ID__']) }}";
        var historiqueUrl = "{{ route('comptes.historique', ['code_compte' => '__ID__']) }}";

        $('#ctxRIB').on('click', function (e) {
            e.preventDefault();
            $ctxMenu.hide();
            if (ctxCode) window.open(ribUrl.replace('__ID__', ctxCode), '_blank');
        });

        $('#ctxReleve').on('click', function (e) {
            e.preventDefault();
            $ctxMenu.hide();
            if (ctxCode) {
                $('#releveCompteCode').text(ctxCode);
                $('#modalReleve').modal('show');
            }
        });

        $('#ctxHistorique').on('click', function (e) {
            e.preventDefault();
            $ctxMenu.hide();
            if (ctxCode) window.location.href = historiqueUrl.replace('__ID__', ctxCode);
        });
        $('#btnGenererReleve').on('click', function () {
            var debut = $('#releveDebut').val();
            var fin   = $('#releveFin').val();
            if (!ctxCode) return;
            if (!debut || !fin) {
                alert('Veuillez sélectionner une période.');
                return;
            }
            var url = releveUrl.replace('__ID__', ctxCode)
                    + '?date_debut=' + encodeURIComponent(debut)
                    + '&date_fin='   + encodeURIComponent(fin);
            window.open(url, '_blank');
            $('#modalReleve').modal('hide');
        });

        /* ── Bouton supprimer inline ─────────────────── */
        $(document).on('click', '.btn-delete-compte', function () {
            triggerDelete($(this).data('id'), $(this).data('url'));
        });

        function triggerDelete(code, url) {
            showUniversalConfirm(
                'Voulez-vous vraiment supprimer le compte <strong>' + code + '</strong> ?',
                function () {
                    $.ajax({ url: url, type: 'POST', data: { _method: 'DELETE' }, dataType: 'json' })
                        .done(function (res) {
                            if (res.success) {
                                showSystemMessage('success', res.message || 'Compte supprimé avec succès.');
                                setTimeout(function () { window.location.reload(); }, 1200);
                            } else {
                                showSystemMessage('error', res.message || 'Erreur.');
                            }
                        })
                        .fail(function (xhr) {
                            handleAjaxFail(xhr, 'Suppression compte');
                        });
                },
                'Confirmation suppression'
            );
        }

    });
}());
</script>
@endpush
