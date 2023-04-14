<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'optional_thumbnail',
        'slug',
        'user_id'
    ];

    protected $visible = [
        'slug',
        'title',
        'description'
    ];

    public $timestamps = false;

    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gameVersions() {
        return $this->hasMany(GameVersion::class);
    }

    public function scoreCount() {
        return $this->hasManyThrough(GameScore::class, GameVersion::class);
    }

    public function deletedGame() {
        return $this->hasOne(DeletedGame::class);
    }
}
