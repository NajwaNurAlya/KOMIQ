<?php

namespace App\Policies;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;

class ChapterPolicy
{
    /**
     * Tentukan apakah user/tamu dapat membaca chapter.
     * Publik hanya dapat membaca chapter yang berstatus approved.
     * Kreator pemilik atau Admin dapat membaca/preview chapter dalam status apapun.
     */
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

    /**
     * Tentukan apakah kreator dapat melihat daftar chapter dari suatu komik.
     */
    public function viewAny(User $user, Comic $comic): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isKreator() && $comic->user_id === $user->id;
    }

    /**
     * Tentukan apakah kreator dapat membuat chapter baru pada komik bersangkutan.
     */
    public function create(User $user, Comic $comic): bool
    {
        return $user->isKreator() && $comic->user_id === $user->id;
    }

    /**
     * Tentukan apakah kreator dapat mengedit chapter.
     * Chapter hanya bisa diedit jika statusnya draft atau rejected.
     */
    public function update(User $user, Chapter $chapter): bool
    {
        $comic = $chapter->comic;
        $isOwner = $comic && $comic->user_id === $user->id;

        return $user->isKreator() && $isOwner && $chapter->canBeEdited();
    }

    /**
     * Tentukan apakah kreator dapat menghapus chapter.
     * Chapter hanya bisa dihapus jika berstatus draft atau rejected.
     */
    public function delete(User $user, Chapter $chapter): bool
    {
        $comic = $chapter->comic;
        $isOwner = $comic && $comic->user_id === $user->id;

        return $user->isKreator() && $isOwner && $chapter->canBeEdited();
    }

    /**
     * Tentukan apakah kreator dapat mengajukan chapter untuk ditinjau (submit for review).
     * Status harus draft atau rejected.
     */
    public function submit(User $user, Chapter $chapter): bool
    {
        $comic = $chapter->comic;
        $isOwner = $comic && $comic->user_id === $user->id;

        return $user->isKreator() && $isOwner && $chapter->canBeSubmitted();
    }

    /**
     * Tentukan apakah pengguna dapat melakukan review (menyetujui atau menolak chapter).
     * Hanya Admin yang memiliki wewenang review.
     */
    public function review(User $user): bool
    {
        return $user->isAdmin();
    }
}
