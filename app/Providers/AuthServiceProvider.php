<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Group;
use App\Policies\GroupPolicy;
use App\User;
use App\Policies\EditProfilePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    //モデルとポリシーを紐付ける
    //Groupモデルに対する処理への認可には、GroupPolicyを使用するということ
    protected $policies = [
        'App\User' => 'App\Policies\EditProfilePolicy',
        'App\Group' => 'App\Policies\GroupPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

       Gate::define('admin', function($user, $group) {
         return $user->id === $group->author_id;
       });
    }
}
