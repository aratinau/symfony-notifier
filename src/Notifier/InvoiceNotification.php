<?php

namespace App\Notifier;

use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class InvoiceNotification extends Notification implements EmailNotificationInterface
{
    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        // TODO: Implement asEmailMessage() method.
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        if (
            $this->price > 10000
            && $recipient instanceof SmsRecipientInterface
        ) {
            return ['sms'];
        }

        return ['email'];
    }


    public function asChatMessage(RecipientInterface $recipient, ?string $transport = null): ?ChatMessage
    {
        // Add a custom subject and emoji if the message is sent to Slack
        if ('slack' === $transport) {
            $this->subject('You\'re invoiced '.strval($this->price).' EUR.');
            $this->emoji("money");
            return ChatMessage::fromNotification($this);
        }

        // If you return null, the Notifier will create the ChatMessage
        // based on this notification as it would without this method.
        return null;
    }
}
