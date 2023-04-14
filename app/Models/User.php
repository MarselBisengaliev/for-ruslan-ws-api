<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'registered_timestamp',
        'last_login_timestamp'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public $timestamps = false;

    public function gameScores() {
        return $this->hasMany(GameScore::class)->orderBy('score', 'desc');
    }

    public function uploadedGames() {
        return $this->hasMany(Game::class);
    }

    public function blocked() {
        return $this->hasOne(BlockedUser::class);
    }
}
