<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function admin($user)
    {
    }

    public function merchant($user)
    {
    }

    /**
     * @param \App\User $user
     * @param string $ability role
     * @return bool
     */
    public function before($user, $ability)
    {
        return $user->role === $ability;
    }
}
