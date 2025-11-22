<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LikeThresholdReachedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public int $likeCount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Alert: User {$this->user->name} has reached {$this->likeCount} likes!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.like-threshold',
            with: [
                'user' => $this->user,
                'likeCount' => $this->likeCount,
            ],
        );
    }
}
