<?php

namespace App\Models;

use App\Traits\FilterSort;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class MailTemplates extends ModelActivityExtend
{
    use SoftDeletes, FilterSort, LogsActivity, CausesActivity;

    const PAGINATE = 10;
    public $timestamps = true;
    const MODULE_NAME = 'custom.mail_templates';
    protected $table = 'mail_template';
    protected $fillable = ['name', 'type', 'content'];

    const PLACEHOLDERS = [
        'to_name'        => ['translation_key' => 'to_name'],
        'date_apply'        => ['translation_key' => 'date_apply'],
        'reg_number'        => ['translation_key' => 'reg_number'],
        'administration'    => ['translation_key' => 'administration'],
        'applicant'         => ['translation_key' => 'applicant_name'],
        'forward_administration' => ['translation_key' => 'forward_administration'],
        'forward_date_apply'        => ['translation_key' => 'forward_date_apply'],
        'new_reg_number'        => ['translation_key' => 'new_reg_number'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    //activity
    protected string $logName = "mail_templates";

    /**
     * Content
     */
    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => !empty($value) ? html_entity_decode($value) : $value,
            set: fn (string|null $value) => !empty($value) ?  htmlentities(stripHtmlTags($value)) : $value,
        );
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }
}
