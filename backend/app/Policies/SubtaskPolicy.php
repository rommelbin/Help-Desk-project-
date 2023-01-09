<?php

namespace App\Policies;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class SubtaskPolicy
{
    use HandlesAuthorization;

    public function showMany(User $user)
    {
        return true;
    }

    public function showOne(User $user, Subtask $subtask)
    {
        $task = Task::findOrFail($subtask->task_id);
        return Gate::allows('isAdmin') || $user->id === $task->executor_id || $user->id === $task->client_id;
    }

    public function storeModel(User $user, Subtask $subtask)
    {
        $task = Task::findOrFail($subtask->task_id);
        return !($task->status === 'Закрыта') && !($task->status === 'Отклонена') && (Gate::allows('isAdmin') || $user->id === $task->executor_id || $user->id === $task->client_id);
    }

    public function updateModel(User $user, Subtask $subtask)
    {
        $task = Task::findOrFail($subtask->task_id);
        return !($task->status === 'Закрыта') && !($task->status === 'Отклонена') && (Gate::allows('isAdmin') || $user->id === $task->executor_id);
    }

    public function deleteModel(User $user, Subtask $subtask)
    {
        $task = Task::findOrFail($subtask->task_id);
        return !($task->status === 'Закрыта') && !($task->status === 'Отклонена') && (Gate::allows('isAdmin') || $user->id === $task->executor_id);
    }
}
