<?php

declare(strict_types=1);

namespace App\Policies;

use App\Profile;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    /**
     * プロフィール編集の権限があるかチェック
     * @param User $user
     * @param Profile $profile
     * @return mixed
     */
    public function view(User $user, Profile $profile)
    {
        return $user->id === $profile->user_id;
    }
}
