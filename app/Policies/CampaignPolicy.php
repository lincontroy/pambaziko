<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Campaign $campaign)
    {
        return $user->id === $campaign->user_id;
    }

    public function update(User $user, Campaign $campaign)
    {
        return $user->id === $campaign->user_id;
    }

    public function delete(User $user, Campaign $campaign)
    {
        return $user->id === $campaign->user_id;
    }
}