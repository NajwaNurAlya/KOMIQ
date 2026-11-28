<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterAccess extends Model
{
    protected $table = 'chapter_access';

    protected $fillable = [
        'user_id',
        'chapter_id',
        'coin_transaction_id',
    ];
}
