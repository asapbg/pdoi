<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileTypeTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'profile_type_id', 'name'];
}
