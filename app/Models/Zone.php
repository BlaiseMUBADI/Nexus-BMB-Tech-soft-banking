<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\RH\Agent;
use App\Models\ZoneAffectation;

class Zone extends Model
    
{
    protected $table = 'tb_zones';
    protected $primaryKey = 'code_zone';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'code_zone',
        'nom',
        'agent_commercial_matricule',
        'commune',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_commercial_matricule', 'matricule');
    }

    public function affectations()
    {
        return $this->hasMany(ZoneAffectation::class, 'code_zone', 'code_zone');
    }

    public function affectationActive()
    {
        $today = now()->toDateString();

        return $this->hasOne(ZoneAffectation::class, 'code_zone', 'code_zone')
            ->whereRaw('UPPER(Etat) = ?', ['ACTIF'])
            ->whereDate('date_debut', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>', $today);
            })
            ->latest('date_debut');
    }

    public function scopeAssignedToAgent(Builder $query, string $matricule): Builder
    {
        return $query->whereHas('affectationActive', function (Builder $q) use ($matricule): void {
            $q->where('agent_matricule', $matricule);
        });
    }

    //public $timestamps = false;
    public static function boot()
    {
        parent::boot();
        static::creating(function ($zone) {
            if (!$zone->code_zone) {
                $annee = date('y');
                $prefix = 'ZON-EBENKGA-' . $annee . '-';
                $codes = self::where('code_zone', 'like', $prefix.'%')
                    ->pluck('code_zone')
                    ->map(function($c) use ($prefix) {
                        return (int)preg_replace('/[^0-9]/', '', str_replace($prefix, '', $c));
                    })->filter()->sort()->values();
                $next = 1;
                foreach ($codes as $num) {
                    if ($num != $next) break;
                    $next++;
                }
                $zone->code_zone = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
