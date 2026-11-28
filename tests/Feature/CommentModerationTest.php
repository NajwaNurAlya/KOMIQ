<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Comic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_reported_comment_enters_admin_queue_and_can_be_kept_visible(): void
    {
        $creator = User::factory()->create(['role' => 'kreator']);
        $reader = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);
        $comic = Comic::create([
            'user_id' => $creator->id,
            'title' => 'Komik Uji Laporan',
            'slug' => 'komik-uji-laporan-' . uniqid(),
            'status' => 'ongoing',
        ]);
        $chapter = Chapter::create([
            'comic_id' => $comic->id,
            'chapter_number' => 1,
            'title' => 'Chapter Uji',
            'pages' => ['tests/page.jpg'],
            'status' => Chapter::STATUS_APPROVED,
            'published_at' => now(),
        ]);
        $comment = Comment::create(['user_id' => $creator->id, 'chapter_id' => $chapter->id, 'content' => 'Komentar untuk diuji.']);

        $this->actingAs($reader)
            ->post(route('comments.report', $comment))
            ->assertRedirect();

        $this->assertDatabaseHas('comment_reports', ['comment_id' => $comment->id, 'reporter_id' => $reader->id, 'status' => 'pending']);
        $this->actingAs($admin)->get(route('admin.comments.index', ['status' => 'pending']))->assertSee('Komentar untuk diuji.');

        $this->actingAs($admin)
            ->patch(route('admin.comments.review', $comment))
            ->assertRedirect();

        $this->assertDatabaseMissing('comment_reports', ['comment_id' => $comment->id, 'status' => 'pending']);
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'is_hidden' => false]);
    }
}
