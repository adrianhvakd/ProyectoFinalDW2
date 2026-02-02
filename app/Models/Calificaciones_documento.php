<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Calificaciones_documento extends BaseModel
{
    use SoftDeletes;

    protected $table = 'calificaciones_documentos';

    protected $fillable = [
        'user_id',
        'documento_id',
        'calificacion',
        'comentario',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documento()
    {
        return $this->belongsTo(Document::class);
    }
}
