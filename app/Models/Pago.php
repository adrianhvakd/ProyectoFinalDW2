<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends BaseModel
{
    use SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'user_id',
        'monto_total',
        'comprobante',
        'estado',
        'verificado_por',
        'fecha_verificacion',
        'motivo_rechazo',
    ];

    protected $casts = [
        'fecha_verificacion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verificador()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    public function intenciones()
    {
        return $this->hasMany(intenciones_compra::class, 'pago_id');
    }
}
