@extends('impressions._layout')

@section('titre', 'Compte de Résultat')

@section('contenu')

<div style="background:#e8f5e9; border:1px solid #a5d6a7; padding:6px 12px; margin-bottom:10px; font-size:9px; color:#2e7d32; border-radius:3px;">
    <strong>Résultat net de la période :</strong>
    <span style="background:{{ $resultatNet >= 0 ? '#1a7a4a' : '#c0392b' }}; color:#fff; padding:2px 8px; border-radius:10px;">
        {{ number_format($resultatNet, 2, ',', ' ') }} ({{ $resultatNet >= 0 ? 'BÉNÉFICE' : 'PERTE' }})
    </span>
</div>

<div style="display:table; width:100%;">
    <div style="display:table-cell; width:49%; vertical-align:top; padding-right:1%;">
        <table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; width:100%;">
            <thead>
                <tr style="background:#c0392b; color:#fff; border:2.5px solid #333;">
                    <th style="padding:5px 6px; width:15%; border:2.5px solid #333;">Compte</th>
                    <th style="padding:5px 6px; width:60%; border:2.5px solid #333;">Charges</th>
                    <th style="padding:5px 6px; width:25%; border:2.5px solid #333; text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($charges as $i => $c)
                <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333;">
                    <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; border:2px solid #333; color:#111;">{{ $c->numero_compte }}</td>
                    <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $c->libelle }}</td>
                    <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#111;">{{ number_format($c->montant, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#fdf0ee; font-weight:bold; border:2.5px solid #333;">
                    <td colspan="2" style="padding:6px 8px; text-align:right; border:2.5px solid #333;">TOTAL CHARGES</td>
                    <td style="padding:6px 8px; text-align:right; border:2.5px solid #333;">{{ number_format($totalCharges, 2, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div style="display:table-cell; width:49%; vertical-align:top; padding-left:1%;">
        <table class="info-table" style="font-size:8.5px; border:2px solid #333; border-collapse:collapse; width:100%;">
            <thead>
                <tr style="background:#1a7a4a; color:#fff; border:2.5px solid #333;">
                    <th style="padding:5px 6px; width:15%; border:2.5px solid #333;">Compte</th>
                    <th style="padding:5px 6px; width:60%; border:2.5px solid #333;">Produits</th>
                    <th style="padding:5px 6px; width:25%; border:2.5px solid #333; text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produits as $i => $p)
                <tr style="{{ $i % 2 === 0 ? 'background:#fff;' : 'background:#f9f9f9;' }} border:2px solid #333;">
                    <td style="padding:4px 6px; font-family:'DejaVu Sans Mono',monospace; border:2px solid #333; color:#111;">{{ $p->numero_compte }}</td>
                    <td style="padding:4px 6px; border:2px solid #333; color:#111;">{{ $p->libelle }}</td>
                    <td style="padding:4px 6px; text-align:right; border:2px solid #333; color:#111;">{{ number_format($p->montant, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#e8f5ee; font-weight:bold; border:2.5px solid #333;">
                    <td colspan="2" style="padding:6px 8px; text-align:right; border:2.5px solid #333;">TOTAL PRODUITS</td>
                    <td style="padding:6px 8px; text-align:right; border:2.5px solid #333;">{{ number_format($totalProduits, 2, ',', ' ') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection
