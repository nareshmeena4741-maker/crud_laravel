<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;
    protected $fillable = ['name','team_size'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
