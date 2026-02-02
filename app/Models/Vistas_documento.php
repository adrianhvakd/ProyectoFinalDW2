<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Vistas_documento extends BaseModel
{
    use SoftDeletes;

    protected $table = 'vistas_documentos';

    protected $fillable = [
        'user_id',
        'documento_id',
        'fecha_vista',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documento()
    {
        return $this->belongsTo(Document::class);
    }
}
