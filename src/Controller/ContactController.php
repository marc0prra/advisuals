<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $name    = trim($request->request->get('name', ''));
            $email   = trim($request->request->get('email', ''));
            $message = trim($request->request->get('message', ''));

            if ($name && $email && $message) {
                $contact = new ContactMessage();
                $contact->setName($name);
                $contact->setEmail($email);
                $contact->setMessage($message);
                $contact->setCreatedAt(new \DateTimeImmutable());

                $em->persist($contact);
                $em->flush();

                try {
                    $mail = (new Email())
                        ->from('noreply@advisuals.fr')
                        ->to('adrienmihajlovic.pro@gmail.com')
                        ->replyTo($email)
                        ->subject("Nouveau message de contact — $name")
                        ->text("De : $name ($email)\n\n$message");
                    $mailer->send($mail);
                } catch (\Throwable) {}
            }

            $this->addFlash('contact_success', 'Votre message a bien été envoyé. Nous vous répondrons sous 24h.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig');
    }
}
