<?php

namespace App\Models;

class intenciones_compra extends BaseModel
{
    protected $table = 'intenciones_compra';

    protected $fillable = [
        'user_id',
        'documento_id',
        'pago_id',
        'precio',
        'estado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function documento()
    {
        return $this->belongsTo(Document::class, 'documento_id');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function compra()
    {
        return $this->hasOne(Compra::class, 'intencion_compra_id');
    }
}
