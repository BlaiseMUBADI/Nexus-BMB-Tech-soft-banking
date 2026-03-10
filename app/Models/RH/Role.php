<?php

namespace App\Models\RH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'tb_roles';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['code', 'nom', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'tb_role_permission',
            'role_code',
            'permission_code',
            'code',
            'code'
        );
    }

    // Génération automatique du code EBEN-ROLx (le plus petit numéro libre)
    public static function boot()
    {
        parent::boot();
        static::creating(function ($role) {
            if (!$role->code) {
                $codes = self::pluck('code')->map(function($c) {
                    return (int)preg_replace('/[^0-9]/', '', $c);
                })->filter()->sort()->values();
                $next = 1;
                foreach ($codes as $num) {
                    if ($num != $next) break;
                    $next++;
                }
                $role->code = 'EBEN-ROL' . $next;
            }
        });
    }
}
