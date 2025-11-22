<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'liked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'liked_at' => 'datetime',
        ];
    }

    /**
     * Get the user who liked.
     *
     * @return BelongsTo
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who was liked.
     *
     * @return BelongsTo
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    /**
     * Check if this like has a mutual match.
     *
     * @return bool
     */
    public function isMutual(): bool
    {
        return self::where('from_user_id', $this->to_user_id)
            ->where('to_user_id', $this->from_user_id)
            ->exists();
    }
}
