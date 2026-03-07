@extends('layouts.app')

@section('page_title', 'Liste des clients')
@section('breadcrumb_parent', 'Clients / Membres')
@section('breadcrumb', 'Liste des clients')

@section('content')
{{-- ===== Mini-dashboard ===== --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total clients</span>
                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-male"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hommes</span>
                    <span class="info-box-number">{{ $stats['hommes'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-female"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Femmes</span>
                    <span class="info-box-number">{{ $stats['femmes'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-camera"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Avec photo</span>
                    <span class="info-box-number">{{ $stats['avec_photo'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Card principale ===== --}}
    <div class="card card-primary card-outline">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
            <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Liste des clients</h3>
            <div class="d-flex align-items-center mt-1 mt-md-0">
                <div class="input-group input-group-sm mr-2" style="width:220px;">
                    <input type="text" id="searchClients" class="form-control" placeholder="Rechercher...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                {{-- Bouton Impression --}}
                <button type="button" class="btn btn-sm btn-secondary mr-2"
                        data-toggle="modal" data-target="#modalImpressionListe"
                        title="Imprimer la liste">
                    <i class="fas fa-print mr-1"></i> Imprimer
                </button>
                <a href="{{ route('clients.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> Nouveau client
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Matricule</th>
                            <th>Zone</th>
                            <th>Nom</th>
                            <th>Postnom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th style="width:70px;">Photo</th>
                            <th style="width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="clientsTableBody">
                        @forelse($clients as $loopIndex => $client)
                            <tr
                                class="ctx-row"
                                data-matricule="{{ $client->matricule }}"
                                data-nom="{{ $client->nom }} {{ $client->postnom }}"
                                data-show-url="{{ route('clients.show', $client->matricule) }}"
                                data-edit-url="{{ route('clients.edit', $client->matricule) }}"
                                data-fiche-url="{{ route('clients.fiche.pdf', $client->matricule) }}"
                                data-delete-url="{{ route('clients.destroy', $client->matricule) }}"
                            >
                                <td>{{ $loopIndex + 1 }}</td>
                                <td><code>{{ $client->matricule }}</code></td>
                                <td>{{ $client->zone->nom ?? '—' }}</td>
                                <td>{{ $client->nom }}</td>
                                <td>{{ $client->postnom ?? '' }}</td>
                                <td>{{ $client->prenom }}</td>
                                <td>{{ $client->email ?? '—' }}</td>
                                <td>{{ $client->telephone ?? '—' }}</td>
                                <td class="text-center">
                                    @if($client->photo)
                                        <img src="{{ route('clients.photo', basename($client->photo)) }}"
                                             alt="Photo"
                                             class="client-photo-thumb"
                                             data-photo-url="{{ route('clients.photo', basename($client->photo)) }}"
                                             data-client-nom="{{ $client->nom }} {{ $client->postnom }} {{ $client->prenom }}"
                                             style="width:46px;height:46px;object-fit:cover;border-radius:4px;cursor:pointer;">
                                    @else
                                        <span class="badge badge-secondary">Aucune</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('clients.show', $client->matricule) }}"
                                       class="btn btn-xs btn-info mr-1" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clients.edit', $client->matricule) }}"
                                       class="btn btn-xs btn-warning mr-1" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('clients.fiche.pdf', $client->matricule) }}"
                                       target="_blank" class="btn btn-xs btn-secondary mr-1" title="Fiche PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-xs btn-danger btn-delete-client"
                                            data-url="{{ route('clients.destroy', $client->matricule) }}"
                                            data-nom="{{ $client->nom }} {{ $client->postnom }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>Aucun client trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted small">
            Total affiché : <span id="countVisible">{{ $clients->count() }}</span> client(s)
        </div>
    </div>
</div>

{{-- ===== Modal Impression Liste ===== --}}
<div class="modal fade" id="modalImpressionListe" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white py-2">
                <h5 class="modal-title"><i class="fas fa-print mr-2"></i>Paramètres d'impression</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formImpressionListe" action="{{ route('clients.liste.pdf') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="row">
                        {{-- Zone --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Zone / Secteur</label>
                                <select name="code_zone" class="form-control form-control-sm">
                                    <option value="">— Toutes les zones —</option>
                                    @foreach($zones as $z)
                                        <option value="{{ $z->code_zone }}">{{ $z->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- Sexe --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Sexe</label>
                                <select name="sexe" class="form-control form-control-sm">
                                    <option value="tous">— Hommes & Femmes —</option>
                                    <option value="M">Hommes uniquement</option>
                                    <option value="F">Femmes uniquement</option>
                                </select>
                            </div>
                        </div>
                        {{-- Plage de dates d'inscription --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Inscrit(e) à partir du</label>
                                <input type="date" name="date_debut" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold small">Inscrit(e) jusqu'au</label>
                                <input type="date" name="date_fin" class="form-control form-control-sm">
                            </div>
                        </div>
                        {{-- Photo --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Photo d'identité</label>
                                <select name="avec_photo" class="form-control form-control-sm">
                                    <option value="tous">Tous (avec ou sans)</option>
                                    <option value="oui">Avec photo uniquement</option>
                                    <option value="non">Sans photo uniquement</option>
                                </select>
                            </div>
                        </div>
                        {{-- Comptes --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">Comptes bancaires</label>
                                <select name="avec_comptes" class="form-control form-control-sm">
                                    <option value="tous">Tous (avec ou sans)</option>
                                    <option value="oui">Avec compte(s) uniquement</option>
                                    <option value="non">Sans aucun compte</option>
                                </select>
                            </div>
                        </div>
                        {{-- État civil --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold small">État civil</label>
                                <select name="etat_civil" class="form-control form-control-sm">
                                    <option value="tous">— Tous —</option>
                                    <option value="Célibataire">Célibataire</option>
                                    <option value="Marié(e)">Marié(e)</option>
                                    <option value="Divorcé(e)">Divorcé(e)</option>
                                    <option value="Veuf/Veuve">Veuf / Veuve</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info py-1 small mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Laissez un champ vide pour ne pas filtrer sur ce critère. Le PDF s'ouvrira dans un nouvel onglet.
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

{{-- ===== Menu contextuel ===== --}}
<div id="contextMenuClient">
    <div class="ctx-header"><i class="fas fa-user mr-2"></i>Actions sur le client</div>
    <div class="ctx-section">
        <ul>
            <li><a href="#" class="ctx-item" id="ctxVoir"><i class="fas fa-eye"></i> Voir la fiche</a></li>
            <li><a href="#" class="ctx-item" id="ctxModif"><i class="fas fa-edit"></i> Modifier</a></li>
            <li><a href="#" class="ctx-item" id="ctxFiche" target="_blank"><i class="fas fa-file-pdf"></i> Imprimer fiche PDF</a></li>
        </ul>
        <div class="ctx-divider"></div>
        <ul>
            <li><a href="#" class="ctx-item ctx-danger" id="ctxDelete"><i class="fas fa-trash-alt"></i> Supprimer</a></li>
        </ul>
    </div>
</div>

{{-- ===== Modal photo partagée ===== --}}
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="photoModalLabel">Photo du client</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="photoModalImg" src="" alt="Photo du client"
                     class="img-fluid rounded shadow"
                     style="max-width:100%;max-height:80vh;background:#222;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    /* ── Context Menu Client ───────────────────── */
    #contextMenuClient {
        display: none;
        position: absolute;
        z-index: 9999;
        background: #2d3748;
        box-shadow: 0 8px 32px rgba(0,0,0,.45);
        border-radius: 12px;
        min-width: 220px;
        border: 1px solid #4a5568;
        overflow: hidden;
    }
    #contextMenuClient .ctx-header {
        padding: 13px 18px;
        font-weight: 600;
        font-size: .97em;
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        color: #fff;
        letter-spacing: .4px;
    }
    #contextMenuClient .ctx-divider {
        border-bottom: 1px solid #4a5568;
        margin: 4px 12px;
    }
    #contextMenuClient .ctx-section { padding: 8px 8px 10px; }
    #contextMenuClient ul { list-style: none; margin: 0; padding: 0; }
    #contextMenuClient ul li { margin-bottom: 2px; }
    #contextMenuClient a.ctx-item {
        display: flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 7px;
        color: #cbd5e0;
        font-size: .93em;
        text-decoration: none;
        transition: background .15s, color .15s;
    }
    #contextMenuClient a.ctx-item:hover { background: #3b82f6; color: #fff; }
    #contextMenuClient a.ctx-item i { margin-right: 9px; width: 16px; text-align: center; opacity: .85; }
    #contextMenuClient a.ctx-item.ctx-danger:hover { background: #e53e3e; color: #fff; }
    #clientsTableBody tr:hover { cursor: context-menu; }
    #clientsTableBody tr.ctx-active { background: rgba(59,130,246,.12) !important; }
</style>
@endpush

@push('js')
<script>
(function () {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    $(function () {

        /* ─── Recherche live ─── */
        $('#searchClients').on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            var count = 0;
            $('#clientsTableBody tr').each(function () {
                var text = $(this).text().toLowerCase();
                var match = !q || text.indexOf(q) !== -1;
                $(this).toggle(match);
                if (match) count++;
            });
            $('#countVisible').text(count);
        });

        /* ─── Affichage photo modal ─── */
        $(document).on('click', '.client-photo-thumb', function () {
            var url = $(this).data('photo-url');
            var nom = $(this).data('client-nom');
            $('#photoModalImg').attr('src', url);
            $('#photoModalLabel').text('Photo — ' + nom);
            $('#photoModal').modal('show');
        });
        $('#photoModal').on('hidden.bs.modal', function () {
            $('#photoModalImg').attr('src', '');
        });

        /* ─── Suppression AJAX ─── */
        function doDelete(url, $row, nom) {
            showUniversalConfirm(
                'Supprimer le client <strong>' + nom + '</strong> ? Cette action est irréversible.',
                function () {
                    $.post(url, { _method: 'DELETE' })
                        .done(function (res) {
                            var parsed = res;
                            if (typeof parsed !== 'object') {
                                try { parsed = JSON.parse((res + '').replace(/^\uFEFF/, '').trim()); } catch(e) {}
                            }
                            if (parsed && parsed.success) {
                                $row.fadeOut(300, function () { $(this).remove(); });
                                var c = parseInt($('#countVisible').text(), 10) - 1;
                                $('#countVisible').text(c);
                                showSystemMessage('success', parsed.message || 'Client supprimé avec succès.');
                            } else {
                                showSystemMessage('error', (parsed && parsed.message) || 'Erreur lors de la suppression.');
                            }
                        })
                        .fail(function (xhr) {
                            var parsed = null;
                            if (!xhr.responseJSON && xhr.responseText) {
                                try { parsed = JSON.parse(xhr.responseText.replace(/^\uFEFF/, '').trim()); } catch(e) {}
                            }
                            var json = xhr.responseJSON || parsed;
                            showSystemMessage('error', (json && json.message) || 'Impossible de supprimer le client.');
                        });
                },
                'Confirmer la suppression'
            );
        }

        $(document).on('click', '.btn-delete-client', function () {
            var url = $(this).data('url');
            var nom = $(this).data('nom');
            doDelete(url, $(this).closest('tr'), nom);
        });

        /* ─── Menu contextuel ─── */
        var $ctx = $('#contextMenuClient');
        var $ctxRow = null;

        function closeCtx() {
            $ctx.hide();
            $('#clientsTableBody tr').removeClass('ctx-active');
        }

        $(document).on('contextmenu', '.ctx-row', function (e) {
            e.preventDefault();
            $ctxRow = $(this);
            $ctxRow.addClass('ctx-active').siblings().removeClass('ctx-active');

            $('#ctxVoir').attr('href',  $ctxRow.data('show-url'));
            $('#ctxModif').attr('href', $ctxRow.data('edit-url'));
            $('#ctxFiche').attr('href', $ctxRow.data('fiche-url'));

            $ctx.css({ left: e.pageX + 'px', top: e.pageY + 'px' }).show();
            $(document).one('click.ctxClient', function () { closeCtx(); });
        });

        $('#ctxDelete').on('click', function (e) {
            e.preventDefault();
            closeCtx();
            if ($ctxRow) {
                doDelete($ctxRow.data('delete-url'), $ctxRow, $ctxRow.data('nom'));
            }
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') closeCtx();
        });

        $ctx.on('contextmenu', function (e) { e.preventDefault(); });

    });
}());
</script>
@endpush