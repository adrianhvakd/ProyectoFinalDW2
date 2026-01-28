<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calificaciones_documento extends Model
{
    use SoftDeletes;

    protected $table = 'calificaciones_documentos';

    protected $fillable = [
        'usuario_id',
        'documento_id',
        'calificacion',
        'comentario',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function documento()
    {
        return $this->belongsTo(Document::class);
    }
}
