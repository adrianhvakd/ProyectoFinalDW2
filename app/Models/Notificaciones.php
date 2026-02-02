<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Notificaciones extends BaseModel
{
    use SoftDeletes;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'titulo',
        'mensaje',
        'leido_en',
        'ruta',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
