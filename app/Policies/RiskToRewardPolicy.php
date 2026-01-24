<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RiskToReward;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiskToRewardPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_risk::to::reward');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RiskToReward $riskToReward): bool
    {
        return $user->can('view_risk::to::reward');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_risk::to::reward');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RiskToReward $riskToReward): bool
    {
        return $user->can('update_risk::to::reward');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RiskToReward $riskToReward): bool
    {
        return $user->can('delete_risk::to::reward');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_risk::to::reward');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, RiskToReward $riskToReward): bool
    {
        return $user->can('force_delete_risk::to::reward');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_risk::to::reward');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, RiskToReward $riskToReward): bool
    {
        return $user->can('restore_risk::to::reward');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_risk::to::reward');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, RiskToReward $riskToReward): bool
    {
        return $user->can('replicate_risk::to::reward');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_risk::to::reward');
    }
}
