<?php

declare(strict_types=1);

namespace App\Policies;

use App\group;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy
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
     * @param \App\User  $user
     * @param \App\group $group
     * @return mixed
     */
    public function view(User $user, Group $group)
    {
        return $user->groups($group);
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
     * @param \App\User  $user
     * @param \App\group $group
     * @return mixed
     */
    public function update(User $user, group $group)
    {
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User  $user
     * @param \App\group $group
     * @return mixed
     */
    public function delete(User $user, group $group)
    {
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\User  $user
     * @param \App\group $group
     * @return mixed
     */
    public function restore(User $user, group $group)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\User  $user
     * @param \App\group $group
     * @return mixed
     */
    public function forceDelete(User $user, group $group)
    {
    }
}
