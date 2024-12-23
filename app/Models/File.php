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
    const CODE_OBJ_APPLICATION_RENEW = 15;
    const CODE_OBJ_MENU_SECTION = 20;
    const CODE_OBJ_PAGE = 21;
    const CODE_OBJ_MESSAGE = 10000;

    const ALLOWED_FILE_EXTENSIONS = ['doc', 'docx', 'xls', 'xlsx', 'pdf', 'p7s', 'p7m', 'zip', 'rar', '7z'];
    const ALLOWED_FILE_EXTENSIONS_MIMES_TYPE = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/pdf',
        'application/pkcs7-signature', 'application/pkcs7-mime', 'application/zip', 'application/x-rar-compressed', 'application/x-rar', 'application/x-7z-compressed']; //

    const PUBLIC_UPLOAD_DIR = 'pages/';
    const PUBLIC_UPLOAD_EXAMPLES_DIR = 'examples/';

    const USER_GUIDE_FILE = 'user_guide.pdf';
    const ADMIN_GUIDE_FILE = 'Pitay_User_Guide_Admin.pdf';

    public function application(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplication::class, 'id', 'id_object');
    }

    public function renew(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplicationRestoreRequest::class, 'id', 'id_object');
    }

    public function event(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PdoiApplicationEvent::class, 'id', 'id_object');
    }
}
