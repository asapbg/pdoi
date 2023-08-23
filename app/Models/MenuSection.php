<?php

namespace App\Models;

use App\Traits\FilterSort;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Support\Facades\DB;

class MenuSection  extends ModelActivityExtend implements TranslatableContract
{
    use FilterSort, Translatable;

    const PAGINATE = 20;
    const MAX_LEVEL = 2;
    const TRANSLATABLE_FIELDS = ['name', 'content', 'meta_keyword', 'meta_title', 'meta_description'];
    const MODULE_NAME = 'custom.menu_section';

    public array $translatedAttributes = self::TRANSLATABLE_FIELDS;

    public $timestamps = true;

    protected $table = 'menu_section';
    //activity
    protected string $logName = "menu_section";

    protected $fillable = ['active', 'parent_id', 'slug', 'order_idx', 'level'];

    public function scopeIsActive($query)
    {
        $query->where('menu_section.active', 1);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(MenuSection::class, 'id', 'parent_id');
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Page::class, 'section_id', 'id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class, 'id_object', 'id')->where('code_object', '=', File::CODE_OBJ_MENU_SECTION);
    }

    /**
     * Get the model name
     */
    public function getModelName() {
        return $this->name;
    }

    public static function translationFieldsProperties(): array
    {
        return array(
            'name' => [
                'type' => 'text',
                'rules' => ['required', 'string', 'max:255']
            ],
            'content' => [
                'type' => 'summernote',
                'rules' => ['nullable', 'string']
            ],
            'meta_title' => [
                'type' => 'text',
                'rules' => ['nullable', 'string', 'max:255']
            ],
            'meta_keyword' => [
                'type' => 'text',
                'rules' => ['nullable', 'string', 'max:255']
            ],
            'meta_description' => [
                'type' => 'text',
                'rules' => ['nullable', 'string', 'max:255']
            ]
        );
    }

    public static function optionsList($ignoreId = 0, $onlyAvailableToAddSub = false)
    {
        return DB::table('menu_section')
            ->select(['menu_section.id', 'menu_section_translations.name'])
            ->join('menu_section_translations', 'menu_section_translations.menu_section_id', '=', 'menu_section.id')
            ->where('menu_section.active', '=', 1)
            ->whereNull('menu_section.deleted_at')
            ->when((int)$ignoreId, function ($q) use($ignoreId){
                return $q->where('menu_section.id', '<>', (int)$ignoreId);
            })
            ->when($onlyAvailableToAddSub, function ($q) use($ignoreId){
                return $q->where('menu_section.level', '<', self::MAX_LEVEL);
            })
            ->where('menu_section_translations.locale', '=', app()->getLocale())
            ->orderBy('menu_section_translations.name', 'asc')
            ->get();
    }

    public static function menu(): array
    {
        $tree = [];
        $items = DB::table('menu_section')
            ->select(['menu_section.id', 'menu_section.slug', 'menu_section_translations.name', 'menu_section.parent_id', 'menu_section.level'])
            ->join('menu_section_translations', 'menu_section_translations.menu_section_id', '=', 'menu_section.id')
            ->where('menu_section.active', '=', 1)
            ->whereNull('menu_section.deleted_at')
            ->where('menu_section_translations.locale', '=', app()->getLocale())
            ->orderBy('menu_section.order_idx', 'asc')
            ->orderBy('menu_section.parent_id', 'asc')
            ->get();
//dd($items);
        if( $items->count() ) {
            foreach ($items as $item) {
                if( is_null($item->parent_id) ) {
                    $tree[] = array(
                        'slug' => $item->slug,
                        'name' => $item->name,
                        'level' => $item->level,
                        'children' => self::menuChildren($item->id, $items)
                    );
                }
            }
        }
        return $tree;
    }

    private static function menuChildren(int $parent, $items): array
    {
        $children = [];
        if( $items->count() ) {
            foreach ($items as $item) {
                if($item->parent_id == $parent) {
                    $children[] = array(
                        'slug' => $item->slug,
                        'name' => $item->name,
                        'level' => $item->level,
                        'children' => self::menuChildren($item->id, $items)
                    );
                }
            }
        }
        return $children;
    }
}

