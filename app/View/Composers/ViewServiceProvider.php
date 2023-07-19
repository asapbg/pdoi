<?php

namespace App\View\Composers;

use App\Models\MenuSection;
use App\Models\Sector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
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
            $menu_sections = Cache::get($currentMenuKey);
            if( is_null($menu_sections) ) {
                $menu_sections = MenuSection::menu();
                Cache::put($currentMenuKey, $menu_sections, 3600);
            }

            $view->with('menu_sections', $menu_sections);
        });

        // Using closure based composers...
        View::composer(['layouts.app', 'layouts.partial.front.header'], function ($view) {
            $vo_font_percent = (int)Session::get('vo_font_percent', 100);
            $vo_high_contrast = (int)Session::get('vo_high_contrast', 0);
            $vo_can_reset = $vo_font_percent != 100 || $vo_high_contrast == 1;
            $view->with([
                    'vo_font_percent' => $vo_font_percent,
                    'vo_high_contrast' => $vo_high_contrast,
                    'vo_can_reset' => $vo_can_reset
                ]);
        });
    }
}
