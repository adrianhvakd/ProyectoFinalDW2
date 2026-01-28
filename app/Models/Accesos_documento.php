<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accesos_documento extends Model
{
    use SoftDeletes;

    protected $table = 'accesos_documentos';

    protected $fillable = [
        'usuario_id',
        'documento_id',
        'compra_id',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function documento()
    {
        return $this->belongsTo(Document::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}
