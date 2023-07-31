<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\EngineManager;
use Laravel\Scout\Searchable;

class File extends Model
{
    use SoftDeletes, Searchable;
    public $timestamps = true;

    const CODE_OBJ_APPLICATION = 13;
    const CODE_OBJ_EVENT = 14;
    const CODE_OBJ_MESSAGE = 10000;

    const ALLOWED_FILE_EXTENSIONS = ['doc', 'docx', 'xsl', 'xslx', 'pdf'];

    public function application(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplication::class, 'id', 'id_object');
    }

    public function event(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplicationEvent::class, 'id', 'id_object');
    }
}
