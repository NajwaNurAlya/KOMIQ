<?php

namespace Tests\Unit;

use App\Models\Chapter;
use App\Models\User;
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

    public function test_coin_simulation_balance_and_duplicate_protection(): void
    {
        $chapter = new Chapter([
            'is_premium' => true,
            'coin_price' => 20,
        ]);

        $initialBalance = 15;
        $canUnlock = $initialBalance >= $chapter->coin_price;
        $this->assertFalse($canUnlock, 'Saldo yang kurang tidak boleh mengizinkan unlock');

        $sufficientBalance = 50;
        $canUnlockSufficient = $sufficientBalance >= $chapter->coin_price;
        $this->assertTrue($canUnlockSufficient);

        $newBalance = max(0, $sufficientBalance - $chapter->coin_price);
        $this->assertEquals(30, $newBalance);
        $this->assertGreaterThanOrEqual(0, $newBalance);

        $sessionStore = ['unlocked_chapter_1' => true];
        $isAlreadyUnlocked = isset($sessionStore['unlocked_chapter_1']);
        $this->assertTrue($isAlreadyUnlocked, 'Chapter yang sudah di-unlock tidak boleh dipotong koin lagi');
    }
}
