<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'attachment',
        'attachment_name',
        'attachment_type',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class , 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class , 'receiver_id');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }

    public function isImage(): bool
    {
        return $this->hasAttachment() && str_starts_with($this->attachment_type ?? '', 'image');
    }

    public function isFile(): bool
    {
        return $this->hasAttachment() && !$this->isImage();
    }

    public function getAttachmentUrl(): ?string
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }

    /**
     * Get all messages in a conversation between two users (ordered oldest → newest).
     */
    public static function conversation(int $userA, int $userB)
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('receiver_id', $userA);
        })->orderBy('created_at');
    }
}
