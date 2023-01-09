<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class CodePolicy
{
    use HandlesAuthorization;

    public function showMany(User $user)
    {
        return Gate::allows('isAdmin');
    }

    public function showOne(User $user)
    {
        return false;
    }

    public function storeModel(User $user)
    {
        return false;
    }

    public function updateModel(User $user)
    {
        return false;
    }

    public function deleteModel(User $user)
    {
        return Gate::allows('isAdmin');
    }
}
