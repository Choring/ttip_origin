<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LikedNotification extends Notification
{
    use Queueable;

    protected $likeable;
    protected $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct($likeable, $liker)
    {
        $this->likeable = $likeable;
        $this->liker = $liker;
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
        $isPost = $this->likeable instanceof \App\Models\Post;
        $postId = $isPost ? $this->likeable->id : $this->likeable->post_id;
        $title = $isPost ? '내 게시글에 띱 👍을 받았습니다.' : '내 댓글에 띱 👍을 받았습니다.';
        $message = $isPost ? $this->likeable->title : $this->likeable->content;

        return [
            'post_id' => $postId,
            'user_name' => $this->liker->name,
            'title' => $title,
            'message' => '“' . mb_substr($message, 0, 20) . (mb_strlen($message) > 20 ? '...' : '') . '”',
            'url' => route('posts.show', $postId),
        ];
    }
}
