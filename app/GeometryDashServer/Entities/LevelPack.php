<?php

namespace App\GeometryDashServer\Entities;

use App\Enums\GDCS\Game\LevelPackDifficulty;
use Illuminate\Database\Eloquent\Model;

class LevelPack extends Model
{
	protected $table = 'gdcs_level_packs';

	protected $casts = [
		'difficulty' => LevelPackDifficulty::class,
	];
}