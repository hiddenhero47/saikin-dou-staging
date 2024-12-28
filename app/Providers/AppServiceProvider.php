<?php

namespace App\Providers;

use App\Models\Broadcast;
use App\Models\Group;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Models\User;
use App\Observers\BroadcastObserver;
use App\Observers\GroupObserver;
use App\Observers\PaymentObserver;
use App\Observers\PaymentPlanObserver;
use App\Observers\UserObserver;
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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::observe(BroadcastObserver::class);
        Group::observe(GroupObserver::class);
        Payment::observe(PaymentObserver::class);
        PaymentPlan::observe(PaymentPlanObserver::class);
        User::observe(UserObserver::class);
    }
}
