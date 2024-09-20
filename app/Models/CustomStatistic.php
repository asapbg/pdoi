<?php

namespace App\Models;

use App\Enums\CustomStatisticTypeEnum;
use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomStatistic extends ModelActivityExtend implements TranslatableContract
{
    use SoftDeletes, FilterSort, Translatable;

    const PAGINATE = 20;
    const TRANSLATABLE_FIELDS = ['name'];
    const MODULE_NAME = 'custom.custom_statistics';
    const ALLOWED_FILE_EXTENSIONS = ['csv'];
    const ALLOWED_FILE_EXTENSIONS_MIMES_TYPE = ['text/csv'];

    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'custom_statistic';
    //activity
    protected string $logName = "custom_statistics";

    protected $fillable = ['type', 'publish_from', 'publish_to', 'data', 'user_id'];


    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }

    public function publishFrom(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => !empty($value) ? databaseDateTime(Carbon::parse($value)) : null
        );
    }

    public function publishTo(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => !empty($value) ? databaseDateTime(Carbon::parse($value)) : null
        );
    }

    public function typeName(): Attribute
    {
        return Attribute::make(
            get: fn () => __('custom.custom_statistics.'.CustomStatisticTypeEnum::keyByValue($this->status))
        );
    }


    public function scopeIsPublished($query){
        $query->whereRaw("date(custom_statistic.publish_from) <= '".Carbon::now()->format('Y-m-d')."'")
            ->where(function ($q){
                $q->whereRaw("date(custom_statistic.publish_to) >= '".Carbon::now()->format('Y-m-d')."'")
                    ->orWhereNull('custom_statistic.publish_to');
            });
    }


    public function author():HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public static function translationFieldsProperties(): array
    {
        return array(
            'name' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:1000']
            ],
        );
    }
}
