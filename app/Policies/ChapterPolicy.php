<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;

class ChapterPolicy
{
    public function view(?User $user, Chapter $chapter): bool
    {
        if ($chapter->isApproved()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return $user->isKreator() && $chapter->comic && $chapter->comic->user_id === $user->id;
    }

    public function viewAny(User $user, Comic $comic): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isKreator() && $comic->user_id === $user->id;
    }

    public function create(User $user, Comic $comic): bool
    {
        return $user->isKreator() && $comic->user_id === $user->id;
    }

    public function update(User $user, Chapter $chapter): bool
    {
        $comic = $chapter->comic;
        $isOwner = $comic && $comic->user_id === $user->id;

        return $user->isKreator() && $isOwner && $chapter->canBeEdited();
    }

    public function delete(User $user, Chapter $chapter): bool
    {
        $comic = $chapter->comic;
        $isOwner = $comic && $comic->user_id === $user->id;

        return $user->isKreator() && $isOwner && $chapter->canBeEdited();
    }

    public function submit(User $user, Chapter $chapter): bool
    {
        $comic = $chapter->comic;
        $isOwner = $comic && $comic->user_id === $user->id;

        return $user->isKreator() && $isOwner && $chapter->canBeSubmitted();
    }

    public function review(User $user): bool
    {
        return $user->isAdmin();
    }
}
