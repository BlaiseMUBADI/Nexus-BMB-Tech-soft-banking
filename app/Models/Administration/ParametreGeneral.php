<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ParametreGeneral extends Model
{
    protected $table = 'tb_parametres_generaux';

    public const SIGNATURE_GERANT = 'signature_gerant_path';

    protected $fillable = [
        'cle',
        'valeur',
        'updated_by_matricule',
    ];

    private const CACHE_TTL = 3600;

    public static function get(string $cle, ?string $default = null): ?string
    {
        return Cache::remember('parametre_general_' . $cle, self::CACHE_TTL, function () use ($cle, $default) {
            return self::where('cle', $cle)->value('valeur') ?? $default;
        });
    }

    public static function set(string $cle, ?string $valeur, ?string $agentMatricule = null): void
    {
        self::updateOrCreate(
            ['cle' => $cle],
            ['valeur' => $valeur, 'updated_by_matricule' => $agentMatricule]
        );

        Cache::forget('parametre_general_' . $cle);
    }
}
