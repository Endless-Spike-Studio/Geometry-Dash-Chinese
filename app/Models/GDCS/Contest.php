<?php

namespace App\Models\GDCS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Contest extends Model
{
    protected $table = 'gdcs_contests';

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ContestParticipant::class);
    }

    public function rules(): Collection
    {
        return collect(!empty($this->rules) ? json_decode($this->rules) : []);
    }
}
