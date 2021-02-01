<?php

declare(strict_types=1);

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EditProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function view(User $user)
    {
        $profile = $user->profiles()->first();
        return $user->id === $profile->user_id;
    }
}
