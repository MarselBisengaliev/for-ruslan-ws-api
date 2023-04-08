<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_version_id',
        'score'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function gameVersion() {
        return $this->belongsTo(GameVersion::class);
    }
}
