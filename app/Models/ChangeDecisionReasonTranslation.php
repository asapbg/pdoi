<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeDecisionReasonTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = ['locale', 'change_decision_reason_id', 'name'];
}
