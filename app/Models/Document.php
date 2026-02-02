<?php

namespace App\Models;

class Document extends BaseModel
{
    protected $table = 'documents';

    protected $fillable = [
        'name',
        'version',
        'price',
        'description',
        'file_path',
        'active',
        'category_id',
    ];

    public function getRouteKeyName()
    {
        return 'id';
    }

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

    public function accesos_documentos()
    {
        return $this->hasMany(Accesos_documento::class, 'documento_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'documento_id');
    }

    public function usuariosConAcceso()
    {
        return $this->belongsToMany(
            User::class,
            'accesos_documentos',
            'documento_id',
            'user_id'
        )->wherePivot('estado', 'activo');
    }
}
