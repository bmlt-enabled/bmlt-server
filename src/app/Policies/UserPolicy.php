<?php

namespace App\Policies;

use App\Interfaces\ServiceBodyRepositoryInterface;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use DeniesDeactivatedUser, HandlesAuthorization;

    private ServiceBodyRepositoryInterface $serviceBodyRepository;

    public function __construct(ServiceBodyRepositoryInterface $serviceBodyRepository)
    {
        $this->serviceBodyRepository = $serviceBodyRepository;
    }

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, User $resourceUser)
    {
        if ($user->id_bigint == $resourceUser->id_bigint) {
            return true;
        }

        if ($user->isServiceBodyAdmin()) {
            return $user->id_bigint == $resourceUser->owner_id_bigint;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function create(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Service body admins can only create users if they are the primary admin of at least one service body
        if ($user->isServiceBodyAdmin()) {
            return $this->serviceBodyRepository->getAdminServiceBodyIds($user->id_bigint)->isNotEmpty();
        }

        return false;
    }

    public function update(User $user, User $resourceUser)
    {
        if ($user->id_bigint == $resourceUser->id_bigint) {
            return true;
        }

        if ($user->isServiceBodyAdmin()) {
            return $user->id_bigint == $resourceUser->owner_id_bigint;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return false;
    }

    public function partialUpdate(User $user, User $resourceUser)
    {
        return $this->update($user, $resourceUser);
    }

    public function delete(User $user, User $resourceUser)
    {
        return $user->isAdmin();
    }
}
