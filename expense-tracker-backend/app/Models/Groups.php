<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups extends Model
{
    /** @use HasFactory<\Database\Factories\GroupsFactory> */
    use HasFactory;

    protected $guarded = [];

    public function expenses()
    {
        return $this->hasMany(Expenses::class);
    }
}
