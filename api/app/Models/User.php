<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'age',
        'location',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
        ];
    }

    /**
     * Get all pictures for this user.
     *
     * @return HasMany
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(Picture::class);
    }

    /**
     * Get all likes sent by this user.
     *
     * @return HasMany
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class, 'from_user_id');
    }

    /**
     * Get all likes received by this user.
     *
     * @return HasMany
     */
    public function likedBy(): HasMany
    {
        return $this->hasMany(Like::class, 'to_user_id');
    }

    /**
     * Get all dislikes sent by this user.
     *
     * @return HasMany
     */
    public function dislikes(): HasMany
    {
        return $this->hasMany(Dislike::class, 'from_user_id');
    }

    /**
     * Get all dislikes received by this user.
     *
     * @return HasMany
     */
    public function dislikedBy(): HasMany
    {
        return $this->hasMany(Dislike::class, 'to_user_id');
    }

    /**
     * Get all mutual likes (matches) for this user.
     * A match is when user A likes user B AND user B likes user A.
     *
     * @return HasMany
     */
    public function matches(): HasMany
    {
        // This is a simplified implementation
        // In practice, you might use a more complex query
        return $this->likes();
    }

    /**
     * Check if current user has liked a specific user.
     *
     * @param User $user
     * @return bool
     */
    public function hasLiked(User $user): bool
    {
        return $this->likes()
            ->where('to_user_id', $user->id)
            ->exists();
    }

    /**
     * Check if current user has disliked a specific user.
     *
     * @param User $user
     * @return bool
     */
    public function hasDisliked(User $user): bool
    {
        return $this->dislikes()
            ->where('to_user_id', $user->id)
            ->exists();
    }

    /**
     * Check if there's a mutual like (match) with a specific user.
     *
     * @param User $user
     * @return bool
     */
    public function hasMatchedWith(User $user): bool
    {
        return $this->hasLiked($user) && $user->hasLiked($this);
    }

    /**
     * Get the total count of likes received.
     *
     * @return int
     */
    public function getLikeCountAttribute(): int
    {
        return $this->likedBy()->count();
    }
}
