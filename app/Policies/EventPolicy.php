<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /** Admin e corretor acessam a listagem. */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'broker']);
    }

    public function view(User $user, Event $event): bool
    {
        return $this->owns($user, $event);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'broker']);
    }

    public function update(User $user, Event $event): bool
    {
        return $this->owns($user, $event);
    }

    public function delete(User $user, Event $event): bool
    {
        return $this->owns($user, $event);
    }

    /** Admin acessa qualquer registro; corretor só os próprios (eventos sem
     * dono, ex.: legados, ficam editáveis só pelo admin). */
    protected function owns(User $user, Event $event): bool
    {
        return $user->isAdmin() || $event->user_id === $user->id;
    }
}
