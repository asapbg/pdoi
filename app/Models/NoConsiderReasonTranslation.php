<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoConsiderReasonTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'no_consider_reason_id', 'name'];
}
