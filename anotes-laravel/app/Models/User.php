<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * Disable updated_at since existing users table only has created_at
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the notes owned by the user.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'user_id', 'id');
    }

    /**
     * Get the reminders owned by the user.
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'user_id', 'id');
    }
}
