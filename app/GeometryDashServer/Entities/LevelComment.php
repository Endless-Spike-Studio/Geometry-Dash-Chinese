<?php

namespace App\GeometryDashServer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelComment extends Model
{
	protected $table = 'gdcs_level_comments';
	protected $fillable = ['account_id', 'comment', 'percent', 'likes'];

	protected $casts = [
		'spam' => 'boolean',
	];

	public function account(): BelongsTo
	{
		return $this->belongsTo(Account::class, 'account_id');
	}
}