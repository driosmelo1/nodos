<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'url','consultado','yaLoConsulte',
    ];

    protected $attributes = [
        'consultado' => '0',
        'yaLoConsulte' => '0',
    ];
}
