<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends BaseModel
{
    use SoftDeletes;

    protected $table = 'compras';

    protected $fillable = [
        'intencion_compra_id',
        'user_id',
        'documento_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documento()
    {
        return $this->belongsTo(Document::class, 'documento_id');
    }

    public function intencion_compra()
    {
        return $this->belongsTo(Intenciones_compra::class, 'intencion_compra_id');
    }

    public function accesoDocumento()
    {
        return $this->hasOne(AccesoDocumento::class, 'compra_id');
    }
}
