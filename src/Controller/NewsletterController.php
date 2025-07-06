<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/newsletter')]
final class NewsletterController extends AbstractController{
    #[Route('/newsletter/subscribe', name: 'newsletter_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Adresse email invalide.');
            return $this->redirectToRoute('home'); // Remplacer 'home' par ta page d'accueil si nécessaire
        }

        $newsletter = new Newsletter();
        $newsletter->setEmail($email);
        $entityManager->persist($newsletter);
        $entityManager->flush();

        $this->addFlash('success', 'Merci pour votre inscription à la newsletter !');
        return $this->redirectToRoute('home');
    }
    
    
    
    
    #[Route(name: 'app_newsletter_index', methods: ['GET'])]
    public function index(NewsletterRepository $newsletterRepository): Response
    {
        return $this->render('newsletter/index.html.twig', [
            'newsletters' => $newsletterRepository->findAll(),
        ]);
    }

    #[Route('/newsletter/inscription', name: 'newsletter_subscribe_from_footer', methods: ['POST'])]
    public function subscribeFromFooter(Request $request, EntityManagerInterface $em): Response
    {
        $email = $request->request->get('email');
        $referer = $request->request->get('referer', '/'); // fallback à "/" si vide

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Email invalide.');
            return $this->redirect($referer);
        }

        $existing = $em->getRepository(Newsletter::class)->findOneBy(['email' => $email]);

        if (!$existing) {
            $newsletter = new Newsletter();
            $newsletter->setEmail($email);
            $em->persist($newsletter);
            $em->flush();
            $this->addFlash('success', 'Vous êtes maintenant inscrit à la newsletter.');
        } else {
            $this->addFlash('info', 'Vous êtes déjà inscrit à la newsletter.');
        }

        return $this->redirect($referer);
    }


    #[Route('/{id}', name: 'app_newsletter_show', methods: ['GET'])]
    public function show(Newsletter $newsletter): Response
    {
        return $this->render('newsletter/show.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_newsletter_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Newsletter $newsletter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_newsletter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('newsletter/edit.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_newsletter_delete', methods: ['POST'])]
    public function delete(Request $request, Newsletter $newsletter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$newsletter->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($newsletter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_newsletter_index', [], Response::HTTP_SEE_OTHER);
    }
}
