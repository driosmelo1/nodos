<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'url','consultado','yaLoConsulte',
    ];

    //valores por defecto si no se les asigna nada
    protected $attributes = [
        'consultado' => '0',
        'yaLoConsulte' => '0',
    ];
}
