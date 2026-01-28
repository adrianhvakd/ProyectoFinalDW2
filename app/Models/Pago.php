<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'usuario_id',
        'compra_id',
        'monto',
        'fecha_pago',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }
}
