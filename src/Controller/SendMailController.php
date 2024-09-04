<?php

namespace App\Controller;

use App\Notification\CommentReviewNotification;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class SendMailController extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        #[Autowire('%admin_email%')] private string $adminEmail,
        private NotifierInterface $notifier,
    ) {
    }

    #[Route('/send/mail', name: 'app_send_mail')]
    public function email(): Response
    {
        $datas = [];

        $this->mailer->send((new NotificationEmail())
            ->subject('New comment posted')
            ->htmlTemplate('emails/comment_notification.html.twig')
            ->from($this->adminEmail)
            ->to($this->adminEmail)
            ->context(['comment' => 'this is comment'])
        );

        // $this->mailer->send($message);

        return $this->render('send_mail/index.html.twig', [
            'controller_name' => 'SendMailController',
            'action' => 'email',
        ]);
    }

    #[Route('/send/notification')]
    public function notification(NotifierInterface $notifier): Response
    {
        // ...

        // Create a Notification that has to be sent
        // using the "email" channel
        $notification = (new Notification('New Invoice', ['email']))
            ->content('You got a new invoice for 15 EUR.')
            //->importance(Notification::IMPORTANCE_LOW)
        ;

        // The receiver of the Notification
        $recipient = new Recipient(
            'elise@noos.fr',
            '0102030405'
        );

        // Send the notification to the recipient
        $notifier->send($notification, $recipient);

        // ...
        return $this->render('send_mail/index.html.twig', [
            'controller_name' => 'SendMailController',
            'action' => 'notification'
        ]);
    }

    #[Route('/send/custom-notification')]
    public function customNotification(NotifierInterface $notifier): Response
    {
        $comment = 'this is comment';

        $notifier->send(
            new CommentReviewNotification($comment),
            ...$this->notifier->getAdminRecipients()
        );

        return $this->render('send_mail/index.html.twig', [
            'controller_name' => 'SendMailController',
            'action' => 'customNotification'
        ]);
    }
}
