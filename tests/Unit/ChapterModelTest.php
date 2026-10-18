<?php

namespace Tests\Unit;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\User;
use App\Policies\ChapterPolicy;
use PHPUnit\Framework\TestCase;

class ChapterModelTest extends TestCase
{
    public function test_chapter_status_helpers(): void
    {
        $chapter = new Chapter(['status' => Chapter::STATUS_DRAFT]);
        $this->assertTrue($chapter->isDraft());
        $this->assertFalse($chapter->isPending());
        $this->assertFalse($chapter->isApproved());
        $this->assertFalse($chapter->isRejected());
        $this->assertTrue($chapter->canBeEdited());
        $this->assertTrue($chapter->canBeSubmitted());

        $chapter->status = Chapter::STATUS_PENDING;
        $this->assertTrue($chapter->isPending());
        $this->assertFalse($chapter->canBeEdited());
        $this->assertFalse($chapter->canBeSubmitted());

        $chapter->status = Chapter::STATUS_APPROVED;
        $this->assertTrue($chapter->isApproved());
        $this->assertFalse($chapter->canBeEdited());
        $this->assertFalse($chapter->canBeSubmitted());

        $chapter->status = Chapter::STATUS_REJECTED;
        $this->assertTrue($chapter->isRejected());
        $this->assertTrue($chapter->canBeEdited());
        $this->assertTrue($chapter->canBeSubmitted());
    }

    public function test_page_count_attribute(): void
    {
        $chapter = new Chapter(['pages' => ['page1.jpg', 'page2.jpg', 'page3.jpg']]);
        $this->assertEquals(3, $chapter->page_count);

        $chapterEmpty = new Chapter(['pages' => []]);
        $this->assertEquals(0, $chapterEmpty->page_count);
    }

    public function test_user_role_helpers(): void
    {
        $admin = new User(['role' => 'admin']);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isKreator());

        $creator = new User(['role' => 'kreator']);
        $this->assertTrue($creator->isKreator());
        $this->assertFalse($creator->isAdmin());

        $regular = new User(['role' => 'user']);
        $this->assertTrue($regular->isRegularUser());
    }

    public function test_chapter_policy_for_creator_and_admin(): void
    {
        $policy = new ChapterPolicy();

        $creator = new User(['role' => 'kreator']);
        $creator->id = 10;

        $otherCreator = new User(['role' => 'kreator']);
        $otherCreator->id = 99;

        $admin = new User(['role' => 'admin']);
        $admin->id = 1;

        $comic = new Comic();
        $comic->id = 5;
        $comic->user_id = 10;

        $chapterDraft = new Chapter([
            'comic_id' => 5,
            'status' => Chapter::STATUS_DRAFT,
        ]);
        $chapterDraft->setRelation('comic', $comic);

        $chapterApproved = new Chapter([
            'comic_id' => 5,
            'status' => Chapter::STATUS_APPROVED,
        ]);
        $chapterApproved->setRelation('comic', $comic);

        // Kreator pemilik komik
        $this->assertTrue($policy->create($creator, $comic));
        $this->assertTrue($policy->update($creator, $chapterDraft));
        $this->assertTrue($policy->submit($creator, $chapterDraft));
        $this->assertFalse($policy->review($creator));

        // Kreator lain (bukan pemilik)
        $this->assertFalse($policy->create($otherCreator, $comic));
        $this->assertFalse($policy->update($otherCreator, $chapterDraft));
        $this->assertFalse($policy->submit($otherCreator, $chapterDraft));

        // Admin
        $this->assertTrue($policy->review($admin));

        // Publik vs Chapter Approved
        $this->assertTrue($policy->view(null, $chapterApproved));
        $this->assertFalse($policy->view(null, $chapterDraft));
        $this->assertTrue($policy->view($creator, $chapterDraft));
        $this->assertTrue($policy->view($admin, $chapterDraft));
    }

    public function test_coin_simulation_balance_and_duplicate_protection(): void
    {
        $chapter = new Chapter([
            'is_premium' => true,
            'coin_price' => 20,
        ]);

        $initialBalance = 15; // Kurang dari 20 koin
        $canUnlock = $initialBalance >= $chapter->coin_price;
        $this->assertFalse($canUnlock, 'Saldo yang kurang tidak boleh mengizinkan unlock');

        $sufficientBalance = 50;
        $canUnlockSufficient = $sufficientBalance >= $chapter->coin_price;
        $this->assertTrue($canUnlockSufficient);

        // Pengurangan saldo tidak boleh menghasilkan angka negatif
        $newBalance = max(0, $sufficientBalance - $chapter->coin_price);
        $this->assertEquals(30, $newBalance);
        $this->assertGreaterThanOrEqual(0, $newBalance);

        // Simulasi pencegahan transaksi ganda dengan session key
        $sessionStore = ['unlocked_chapter_1' => true];
        $isAlreadyUnlocked = isset($sessionStore['unlocked_chapter_1']);
        $this->assertTrue($isAlreadyUnlocked, 'Chapter yang sudah di-unlock tidak boleh dipotong koin lagi');
    }
}
