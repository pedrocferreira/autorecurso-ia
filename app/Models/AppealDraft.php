<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppealDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'form_data',
        'transaction_id',
        'status',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function transaction()
    {
        return $this->belongsTo(CreditTransaction::class, 'transaction_id');
    }
} 