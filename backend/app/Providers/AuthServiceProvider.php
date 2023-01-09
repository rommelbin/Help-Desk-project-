<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Department;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\SubtaskPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Department::class, DepartmentPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Subtask::class, SubtaskPolicy::class);

        Gate::define('isAdmin', function ($user) {
            return $user->role == 'Администратор';
        });

        Gate::define('isClient', function ($user) {
            return $user->role == 'Заказчик';
        });

        Gate::define('isExecutor', function ($user) {
            return $user->role == 'Исполнитель';
        });

        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}
