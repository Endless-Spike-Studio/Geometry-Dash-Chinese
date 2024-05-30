<?php

namespace App\GeometryDashServer\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyLevel extends Model
{
	protected $table = 'gdcs_weekly_levels';

	protected $fillable = ['level_id', 'apply_at'];

	protected $dates = ['apply_at'];

	public function level(): BelongsTo
	{
		return $this->belongsTo(Level::class);
	}
}