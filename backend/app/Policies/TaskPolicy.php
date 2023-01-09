<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class TaskPolicy
{
    use HandlesAuthorization;

    public function showMany(User $user): bool
    {
        return true;
    }

    public function showOne(User $user, Task $task): bool
    {
        return Gate::allows('isAdmin') || $user->id === $task->executor_id || $user->id === $task->client_id;
    }

    public function storeModel(User $user): bool
    {
        return true;
    }

    public function updateModel(User $user, Task $task): bool
    {
        $attr = key($task->custom_attributes);
        if (Gate::allows('isExecutor') && $attr === 'status' || $attr === 'deadline')
            return true;

        return $user->id === $task->client_id || Gate::allows('isAdmin');
    }

    public function deleteModel(User $user, Task $task): bool
    {
        return !($task->status === 'Отклонена') && (Gate::allows('isAdmin') || $user->id === $task->executor_id || $user->id === $task->client_id);
    }
}
