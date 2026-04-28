<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(\App\Repositories\Contracts\UserRepositoryInterface::class, \App\Repositories\Eloquent\EloquentUserRepository::class);
        $this->app->bind(\App\Repositories\Contracts\SingerRepositoryInterface::class, \App\Repositories\Eloquent\EloquentSingerRepository::class);
        $this->app->bind(\App\Repositories\Contracts\InstrumentRepositoryInterface::class, \App\Repositories\Eloquent\EloquentInstrumentRepository::class);
        $this->app->bind(\App\Repositories\Contracts\BookingRepositoryInterface::class, \App\Repositories\Eloquent\EloquentBookingRepository::class);
        $this->app->bind(\App\Repositories\Contracts\CartRepositoryInterface::class, \App\Repositories\Eloquent\EloquentCartRepository::class);
        $this->app->bind(\App\Repositories\Contracts\OrderRepositoryInterface::class, \App\Repositories\Eloquent\EloquentOrderRepository::class);
    }
    public function boot()
    {
    }
}
