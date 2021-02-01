<?php

declare(strict_types=1);

namespace App\Policies;

use App\profile;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User    $user
     * @param \App\profile $profile
     * @return mixed
     */
    public function view(User $user, Profile $profile)
    {
        return $user->id === $profile->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User    $user
     * @param \App\profile $profile
     * @return mixed
     */
    public function update(User $user, Profile $profile)
    {
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User    $user
     * @param \App\profile $profile
     * @return mixed
     */
    public function delete(User $user, profile $profile)
    {
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\User    $user
     * @param \App\profile $profile
     * @return mixed
     */
    public function restore(User $user, profile $profile)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\User    $user
     * @param \App\profile $profile
     * @return mixed
     */
    public function forceDelete(User $user, profile $profile)
    {
    }
}
