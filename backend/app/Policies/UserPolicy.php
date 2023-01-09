<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class UserPolicy
{
    use HandlesAuthorization;

    public function showMany(User $user): bool
    {
        return true;
    }
    public function showOne(User $user): bool
    {
        return true;
    }
    public function storeModel(User $user): bool
    {
        return Gate::allows('isAdmin');
    }

    public function updateModel(User $user): bool
    {
        return Gate::allows('isAdmin');
    }

    public function deleteModel(User $user, User $user_to_delete): bool
    {
        if (User::find($user_to_delete->id)->email === 'helpdesk@smartworld.team')
            return false;

        return Gate::allows('isAdmin');
    }

    public function sendMailModel(User $user): bool
    {
        return Gate::allows('isAdmin');
    }
}
