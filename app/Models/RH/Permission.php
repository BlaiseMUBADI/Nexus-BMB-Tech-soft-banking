<?php

namespace App\Models\RH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table = 'tb_permissions';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['code', 'nom', 'description'];

    // Génération automatique du code EBEN-PERx (le plus petit numéro libre)
    public static function boot()
    {
        parent::boot();
        static::creating(function ($permission) {
            if (!$permission->code) {
                $codes = self::pluck('code')->map(function($c) {
                    return (int)preg_replace('/[^0-9]/', '', $c);
                })->filter()->sort()->values();
                $next = 1;
                foreach ($codes as $num) {
                    if ($num != $next) break;
                    $next++;
                }
                $permission->code = 'EBEN-PER' . $next;
            }
        });
    }
}
