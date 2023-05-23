<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity;

class CustomActivity extends Activity
{
    const PAGINATE = 20;

    const MODULE_NAME = "Активност";

    /**
     * Always get activity with relations cause and subject
     */
    protected $with = ['causer','subject'];

    /**
     * Get the subject name according to the type
     *
     * @return mixed|string
     */
    public function getSubjectName()
    {
        $subject = $this->subject;

        if ($subject) {
            return $subject->getModelName();
        }

        return "Няма данни за Обекта, може би е бил изтрит";
    }

    /**
     * Get activity description
     *
     * @return mixed|string
     */
    public function getActivityDescription()
    {
        $description = ($this->description == "deleted") ? __('custom.deletion') : __('custom.'.$this->description);
        if (strstr($description, 'custom')) {
            return "Неизвестно действие";
        }

        return $description;
    }

    /**
     * Get the user that has created the nomenclature
     *
     * @param $nomenclature
     * @return User
     */
    public static function getCreator($nomenclature)
    {
        $log = CustomActivity::where('subject_type', get_class($nomenclature))
            ->where('subject_id', $nomenclature->id)
            ->where('description', '=', 'created')
            ->first();

        return ($log) ? $log->causer : null;
    }

    /**
     * Get the user that has made the latest update of the nomenclature
     *
     * @param $nomenclature
     * @return User
     */
    public static function getUpdater($nomenclature)
    {
        $log = CustomActivity::where('subject_type', get_class($nomenclature))
            ->where('subject_id', $nomenclature->id)
            ->where('description', '=', 'updated')
            ->orderBy('created_at', 'desc')
            ->first();

        return ($log) ? $log->causer : null;
    }

    /**
     * Get the user that has deleted the nomenclature
     *
     * @param $nomenclature
     * @return User
     */
    public static function getDeleter($nomenclature)
    {
        $log = CustomActivity::where('subject_type', get_class($nomenclature))
            ->where('subject_id', $nomenclature->id)
            ->where('description', '=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->first();

        return ($log) ? $log->causer : null;
    }
}
