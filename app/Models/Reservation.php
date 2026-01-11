<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'reservation_date',
        'reservation_time',
        'duration',
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'payment_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
