<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Accesos_documento extends BaseModel
{
    use SoftDeletes;

    protected $table = 'accesos_documentos';

    protected $fillable = [
        'user_id',
        'documento_id',
        'compra_id',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documento()
    {
        return $this->belongsTo(Document::class, 'documento_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function intenciones_compra()
    {
        return $this->belongsTo(Intenciones_compra::class, 'intenciones_compra_id');
    }
}
