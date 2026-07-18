@extends('impressions._layout')

@section('titre', 'Bilan au ' . \Carbon\Carbon::parse($dateFin)->format('d/m/Y'))

@section('contenu')

<div style="background:#f0f4f8; border:1px solid #c8d4e0; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#444; border-radius:3px;">
    Bilan calculé sur la totalité des écritures enregistrées à la date du {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}.
    &nbsp;&nbsp;<strong>{{ abs($totalActif - $totalPassif) < 0.01 ? 'BILAN ÉQUILIBRÉ' : 'ATTENTION : DÉSÉQUILIBRE' }}</strong>
</div>

<div style="display:table; width:100%;">
    <div style="display:table-cell; width:49%; vertical-align:top; padding-right:1%;">
        <table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; width:100%;">
            <thead>
                <tr style="background:#1a5276; color:#fff; border:2.5px solid #333;">
                    <th style="padding:5px 6px; width:15%; border:2.5px solid #333;">Compte</th>
                    <th style="padding:5px 6px; width:60%; border:2.5px solid #333;">ACTIF</th>
                    <th style="padding:5px 6px; width:25%; border:2.5px solid #333; text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actif as $i => $a)
                <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333;">
                    <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; border:2px solid #333; color:#111;">{{ $a->numero_compte }}</td>
                    <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $a->libelle }}</td>
                    <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#111;">{{ number_format($a->montant, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#eaf2fa; font-weight:bold; border:2.5px solid #333;">
                    <td colspan="2" style="padding:6px 8px; text-align:right; border:2.5px solid #333;">TOTAL ACTIF</td>
                    <td style="padding:6px 8px; text-align:right; border:2.5px solid #333;">{{ number_format($totalActif, 2, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div style="display:table-cell; width:49%; vertical-align:top; padding-left:1%;">
        <table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; width:100%;">
            <thead>
                <tr style="background:#6c3483; color:#fff; border:2.5px solid #333;">
                    <th style="padding:5px 6px; width:15%; border:2.5px solid #333;">Compte</th>
                    <th style="padding:5px 6px; width:60%; border:2.5px solid #333;">PASSIF</th>
                    <th style="padding:5px 6px; width:25%; border:2.5px solid #333; text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($passif as $i => $p)
                <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333;">
                    <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; border:2px solid #333; color:#111;">{{ $p->numero_compte }}</td>
                    <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $p->libelle }}</td>
                    <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#111;">{{ number_format($p->montant, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f5eef8; font-weight:bold; border:2.5px solid #333;">
                    <td colspan="2" style="padding:6px 8px; text-align:right; border:2.5px solid #333;">TOTAL PASSIF</td>
                    <td style="padding:6px 8px; text-align:right; border:2.5px solid #333;">{{ number_format($totalPassif, 2, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
