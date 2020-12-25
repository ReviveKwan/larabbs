<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        if (env('APP_ENV') == 'local') {
            DB::listen(function ($query)
            {
                $sql = str_replace(["?", "%Y-%m-%d", "%Y-%m"], ["'%s'", "?Y-?m-?d", "?Y-?m"], $query->sql);
                $sql = vsprintf($sql, $query->bindings);
                $sql = str_replace(["\\", "?Y-?m-?d", "?Y-?m"], ["", "%Y-%m-%d", "%Y-%m"], $sql);
                Log::info($sql);
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);

        //
    }
}
