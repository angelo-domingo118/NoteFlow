<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Notebook;
use App\Models\User;

class NotebookPolicy
{
    /**
     * Determine whether the user can view the notebook.
     */
    public function view(User $user, Notebook $notebook): bool
    {
        return $user->id === $notebook->user_id;
    }

    /**
     * Determine whether the user can create notebooks.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the notebook.
     */
    public function update(User $user, Notebook $notebook): bool
    {
        return $user->id === $notebook->user_id;
    }

    /**
     * Determine whether the user can delete the notebook.
     */
    public function delete(User $user, Notebook $notebook): bool
    {
        return $user->id === $notebook->user_id;
    }
}
