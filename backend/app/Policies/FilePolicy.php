<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilePolicy
{
    use HandlesAuthorization;

    public function showMany(User $user)
    {
        return true;
    }

    public function showOne(User $user, File $file)
    {
        return true;
    }

    public function storeModel(User $user)
    {
        return true;
    }
    public function downloadModel(User $user, File $file)
    {
        return true;
    }

    public function deleteModel(User $user, File $modelsFile)
    {
        return true;
    }
}
