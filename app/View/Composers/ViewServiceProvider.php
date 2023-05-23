<?php

namespace App\View\Composers;

use App\Models\Sector;
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
    }
}
