<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    /** Admin e corretor acessam a listagem. */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'broker']);
    }

    public function view(User $user, Listing $listing): bool
    {
        return $this->owns($user, $listing);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'broker']);
    }

    public function update(User $user, Listing $listing): bool
    {
        return $this->owns($user, $listing);
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $this->owns($user, $listing);
    }

    /** Admin acessa qualquer registro; corretor só os próprios. */
    protected function owns(User $user, Listing $listing): bool
    {
        return $user->isAdmin() || $listing->user_id === $user->id;
    }
}
