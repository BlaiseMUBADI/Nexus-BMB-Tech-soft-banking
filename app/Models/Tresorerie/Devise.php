<?php

namespace App\Models\Tresorerie;

use Illuminate\Database\Eloquent\Model;

class Devise extends Model
{
    protected $table = 'tb_devises';
    protected $primaryKey = 'code_iso';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'code_iso', 'nom', 'symbole', 'est_reference'
    ];
}
