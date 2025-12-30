<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'profile_picture',
        'address',
        'contact_number',
        'gender',
        'birth_date',
    ];

    /**
     * Append accessors to model's array form
     *
     * @var array
     */
    protected $appends = ['profile_picture_url'];

    /**
     * Get the profile picture URL
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function profilePictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->profile_picture
                ? asset('storage/' . $this->profile_picture)
                : null,
        );
    }

    /**
     * Get the user that owns the UserProfile
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
