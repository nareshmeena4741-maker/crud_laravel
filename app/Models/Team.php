<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $fillable = ['tournament_id','team_name'];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
