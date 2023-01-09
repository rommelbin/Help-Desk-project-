<?php

namespace App\Policies;

use App\Models\Comment;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CommentPolicy
{
    use HandlesAuthorization;

    public function showMany(User $user, Task $task)
    {
        return $user->id === $task->executor_id || $user->id === $task->client_id || Gate::allows('isAdmin');
    }

    public function showOne(User $user, Comment $comment)
    {
        return true;
    }

    public function storeModel(User $user, Comment $comment)
    {
        $task = Task::findOrFail($comment->task_id);
        return !($task->status === 'Закрыта') && !($task->status === 'Отклонена') && (Gate::allows('isAdmin') || $user->id === $task->executor_id || $user->id === $task->client_id);
    }

    public function updateModel(User $user, Comment $comment)
    {
        throw new \Exception('Comment is not updatable', Response::HTTP_NOT_FOUND);
    }

    public function deleteModel(User $user, Comment $comment)
    {
        return $user->id === $comment->user_id || Gate::allows('isAdmin');
    }
}
