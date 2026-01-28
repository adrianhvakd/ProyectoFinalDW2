<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'name',
        'version',
        'price',
        'description',
        'file_path',
        'active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function vistas_documentos()
    {
        return $this->hasMany(Vistas_documento::class, 'documento_id');
    }

    public function calificaciones_documentos()
    {
        return $this->hasMany(Calificaciones_documento::class, 'documento_id');
    }
}
