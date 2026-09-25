<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
    ];
}
