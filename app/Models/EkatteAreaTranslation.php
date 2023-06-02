<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkatteAreaTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'ekatte_area_id', 'ime'];
}
