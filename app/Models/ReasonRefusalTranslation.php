<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReasonRefusalTranslation  extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'reason_refusal_id', 'name'];
}

