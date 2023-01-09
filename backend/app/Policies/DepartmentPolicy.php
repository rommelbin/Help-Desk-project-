<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function showMany(User $user)
    {
        return true;
    }

    public function showOne(User $user)
    {
        return Gate::allows('isAdmin');
    }

    public function storeModel(User $user)
    {
        return Gate::allows('isAdmin');
    }

    public function updateModel(User $user)
    {
        return Gate::allows('isAdmin');
    }

    public function deleteModel(User $user)
    {
        return Gate::allows('isAdmin');
    }
}
