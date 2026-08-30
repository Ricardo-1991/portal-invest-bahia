<?php

namespace App\Policies;

use App\Models\PageContent;
use App\Models\User;

class PageContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, PageContent $pageContent): bool
    {
        return $user->isAdmin() && $pageContent->key === 'home';
    }

    /** Páginas fixas são criadas exclusivamente pelos desenvolvedores. */
    public function create(User $user): bool
    {
        return false;
    }

    /** No painel, somente a mídia da Home pode ser atualizada. */
    public function update(User $user, PageContent $pageContent): bool
    {
        return $user->isAdmin() && $pageContent->key === 'home';
    }

    /** Páginas fixas não podem ser excluídas pelo painel. */
    public function delete(User $user, PageContent $pageContent): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
