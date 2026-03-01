<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TauxEchange extends Model
{
    protected $table = 'tb_taux_echanges';
    protected $fillable = [
        'devise_source', 'devise_destination', 'taux', 'date_application'
    ];
    
}
