<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // Thêm dòng này
use Illuminate\Broadcasting\PrivateChannel;

class NewNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $message;
    public $user;

    /**
     * Create a new notification instance.
     */
     public function __construct($message, $user)
    {
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'sender' => auth()->user()->name ?? 'System', // Hoặc thông tin người gửi
        ];
    }

    public function toBroadcast($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\BroadcastMessage([
            'message' => $this->message,
            'sender' => auth()->user()->name ?? 'System',
            'timestamp' => now()->toDateTimeString(),
        ]))->onConnection('reverb'); // Sử dụng Reverb connection
    }

    public function broadcastOn()
    {
        // Kênh riêng tư cho từng người dùng
        return new PrivateChannel('users.' . $this->user->id);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new-notification'; // Tên event mà frontend sẽ lắng nghe
    }
}
