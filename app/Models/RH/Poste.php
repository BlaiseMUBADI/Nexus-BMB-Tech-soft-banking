<?php

namespace App\Models\RH;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    protected $table = 'tb_postes';
    use HasFactory;

    protected $fillable = [
        'service_id',
        'nom',
        'description',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
