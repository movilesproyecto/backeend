<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'department_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'uploaded_by',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'file_size' => 'integer'
    ];

    /**
     * Relación con Department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relación con User (quien subió la imagen)
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Obtener la URL pública de la imagen
     */
    public function getUrlAttribute()
    {
        return url('/storage/' . $this->file_path);
    }
}
