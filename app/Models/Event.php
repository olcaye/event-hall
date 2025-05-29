<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'location',
        'date',
        'start_time',
        'end_time',
        'latitude',
        'longitude',
        'booking_limit',
        'cover_image',
        'gallery',
        'user_id',
    ];

    protected $attributes = [
        'status' => EventStatus::ACTIVE,
    ];

    protected function casts(): array
    {
        return [
            'gallery'    => 'array',
            'date'       => 'date',
            'start_time' => 'datetime:H:i',
            'end_time'   => 'datetime:H:i',
            'status'     => EventStatus::class,
        ];
    }

    protected function formattedDate(): Attribute
    {
        return Attribute::get(fn() => $this->date->format('d M Y'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getCoverImageUrlAttribute()
    {
        return $this->cover_image ? Storage::url($this->cover_image) : null;
    }

    public function getGalleryImageUrlsAttribute()
    {
        if (empty($this->gallery)) {
            return [];
        }
        return array_map(function ($path) {
            return Storage::url($path);
        }, $this->gallery);
    }

    public function isActive(): bool
    {
        return $this->status === EventStatus::ACTIVE;
    }

    public function isEnded(): bool
    {
        return $this->status === EventStatus::ENDED;
    }

    public function isCancelled(): bool
    {
        return $this->status === EventStatus::CANCELLED;
    }

    public function isSuspended(): bool
    {
        return $this->status === EventStatus::SUSPENDED;
    }

    public function isPending(): bool
    {
        return $this->status === EventStatus::PENDING;
    }
}
