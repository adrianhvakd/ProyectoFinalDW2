<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificaciones extends Model
{
    use SoftDeletes;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'mensaje',
        'leido_en',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
