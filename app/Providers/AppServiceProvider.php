<?php

namespace App\Providers;

use App\Services\Database;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Unread contact messages for the admin sidebar badge
        View::composer('admin.layout', function ($view) {
            try {
                $unread = (int) (Database::first('SELECT COUNT(*) AS c FROM contact_messages WHERE read_at IS NULL')->c ?? 0);
            } catch (Throwable) {
                $unread = 0; // table not migrated yet
            }

            $view->with('unreadMessages', $unread);
        });
    }
}
