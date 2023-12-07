<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(\App\Models\Page::get()->count()){
            $faqPage = [
                'slug' => Page::ADMIN_PAGE,
                'system_name' => Page::ADMIN_PAGE,
                'name' => 'Помощна информация (администарция)',
                'content' => 'Помощна информация (администарция)'
            ];

            $dbPage = Page::where('slug', '=', $faqPage['slug'])->first();
            if(!$dbPage) {
                $locales = config('available_languages');
                $item = Page::create([
                    'slug' => $faqPage['slug'],
                    'system_name' => $faqPage['system_name']
                ]);

                if( $item ) {
                    foreach ($locales as $locale) {
                        $item->translateOrNew($locale['code'])->name = $faqPage['name'];
                        $item->translateOrNew($locale['code'])->content = $faqPage['content'];
                    }
                    $item->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
