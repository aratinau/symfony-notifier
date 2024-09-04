<?php

namespace App\Notification;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;

class CommentReviewNotification extends Notification implements EmailNotificationInterface
{
    public function __construct(
        private $comment,
    ) {
        parent::__construct('New comment posted');
        //$this->importance(Notification::IMPORTANCE_LOW);
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient);

        // see: vendor/symfony/notifier/Notification/Notification.php
        // see: vendor/symfony/twig-bridge/Mime/NotificationEmail.php
        /*
             private array $context = [
                'importance' => self::IMPORTANCE_LOW,
                'content' => '',
                'exception' => false,
                'action_text' => null,
                'action_url' => null,
                'markdown' => false,
                'raw' => false,
                'footer_text' => 'Notification email sent by Symfony',
            ];
         */

        $message->getMessage()
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->context([
                'comment' => $this->comment,
                'footer_text' => 'Notification email sent by Symfony Notifier',
                'importance' => ''
            ])
        ;

        return $message;
    }
}
