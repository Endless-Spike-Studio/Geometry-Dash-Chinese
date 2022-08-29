<?php

namespace App\Models\GDCS;

use Illuminate\Database\Eloquent\Model;

class LikeRecord extends Model
{
    protected $table = 'gdcs_like_records';
    protected $fillable = ['type', 'item_id', 'user_id', 'ip'];
}
