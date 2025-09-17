<?php

namespace App\Policies;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UploadPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Upload $upload)
    {
        return $user->id === $upload->user_id;
    }

    public function update(User $user, Upload $upload)
    {
        return $user->id === $upload->user_id;
    }

    public function delete(User $user, Upload $upload)
    {
        return $user->id === $upload->user_id;
    }
}