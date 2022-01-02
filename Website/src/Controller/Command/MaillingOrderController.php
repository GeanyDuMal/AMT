<?php

namespace App\Controller\Command;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MaillingOrderController extends AbstractController
{
    /**
     * @Route("/command/menu/mailling/{id}", name="orderDelete", methods={"GET", "DELETE"})
     */
    public function sendEmail($id, MailerInterface $mailer){
        if ($this->isGranted('ROLE_TRESORIER') || !$this->isGranted('ROLE_ASSOC')){
            return $this->redirectToRoute('home');
        }
        $email = new Email();
        $email->from('hello@example.com')
            ->to('you@example.com')
            ->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
        }
        return new JsonResponse(true);
    }
}