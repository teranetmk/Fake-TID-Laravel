<?php

    namespace App\Http\Notifications;

    use Illuminate\Notifications\Notification;
    use Illuminate\Notifications\Messages\MailMessage;

    use Illuminate\Auth\Notifications\ResetPassword;

    class ResetPasswordNotification extends ResetPassword
    {
        public function toMail($notifiable)
        {
            if (static::$toMailCallback) {
                return call_user_func(static::$toMailCallback, $notifiable, $this->token);
            }

            return (new MailMessage)
                ->subject(__('mail.reset.password.subject'))
                ->line(__('mail.reset.password.line'))
                ->action(__('mail.reset.password.button'), url(config('app.url').route('password.reset', $this->token, false)))
                ->line(__('mail.reset.password.warning'));
        }
    }