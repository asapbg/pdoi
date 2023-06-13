<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtendTermsReasonTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'category_id', 'name'];
}
