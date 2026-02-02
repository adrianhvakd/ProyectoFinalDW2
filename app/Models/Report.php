<?php

namespace App\Models;

class Report extends BaseModel
{
    protected $table = 'reports';

    protected $fillable = [
        'name',
        'file_path',
        'creado_por',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
