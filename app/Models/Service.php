<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Service extends Model
{
    use HasFactory;

    protected $table = 'tb_services';

    protected $fillable = [
        'nom',
        'description',
    ];

    public function postes()
    {
        return $this->hasMany(Poste::class, 'service_id');
    }
}
