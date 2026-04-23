<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplied extends Notification
{
    use Queueable;

    protected $comment;
    protected $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $reply)
    {
        $this->comment = $comment;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->comment->post_id,
            'comment_id' => $this->reply->id,
            'user_name' => $this->reply->user->name,
            'title' => '내 댓글에 새로운 답글이 달렸습니다.',
            'message' => '“' . mb_substr($this->reply->content, 0, 20) . (mb_strlen($this->reply->content) > 20 ? '...' : '') . '”',
            'url' => route('posts.show', $this->comment->post_id),
        ];
    }
}
