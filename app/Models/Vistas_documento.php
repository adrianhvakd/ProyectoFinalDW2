<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vistas_documento extends Model
{
    use SoftDeletes;

    protected $table = 'vistas_documentos';

    protected $fillable = [
        'usuario_id',
        'documento_id',
        'fecha_vista',
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
