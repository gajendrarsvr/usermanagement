<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResets extends Model
{
    use HasFactory;

    public $table = 'password_resets';

    protected $dates = [
        'created_at',
    ];

    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];
}
