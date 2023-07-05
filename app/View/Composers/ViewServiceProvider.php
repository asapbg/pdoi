<?php

namespace App\View\Composers;

use App\Models\MenuSection;
use App\Models\Sector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        // Using closure based composers...
        View::composer('sidebar', function ($view) {

            $sectors = Sector::select('id', 'name_bg', 'abbr_bg')
                ->whereActive(true)
                ->get();

            $view->with('sectors', $sectors);
        });

        // Using closure based composers...
        View::composer('layouts.partial.front.top_menu', function ($view) {
            $currentMenuKey = 'menu_'.app()->getLocale();
            $menu_sections = null;
            if( is_null($menu_sections) ) {
                $menu_sections = MenuSection::menu();
                Cache::put($currentMenuKey, $menu_sections, 3600);
            }

            $view->with('menu_sections', $menu_sections);
        });
    }
}
