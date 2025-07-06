<?php



namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Newsletter;
use App\Entity\BillingAdress;
use App\Entity\ShippingAdress;
use App\Form\BillingAdressType;
use App\Form\ShippingAdressType;
use App\Repository\OrderRepository;
use App\Form\ChangePasswordFormType;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountController extends AbstractController
{
    #[Route('/compte', name: 'app_account')]
    public function index( NewsletterRepository $newsletterRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        

        $shippingAdresses = $user->getShippingAdresses(); // attention au nom !
        $billingAdress = $user->getBillingAdress();
        $isSubscribed = $newsletterRepo->findOneBy(['email' => $user->getEmail()]) !== null;

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'shippingAdresses' => $shippingAdresses,
            'billingAdress' => $billingAdress,
            'isSubscribedToNewsletter' => $isSubscribed,
        ]);
    }

    #[Route('/mon-compte/infos', name: 'app_account_infos')]
    
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/mot-de-passe/modifier', name: 'app_account_password_edit')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('L’utilisateur connecté n’est pas valide.');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été mis à jour avec succès.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        // S’inscrire à la newsletter
    #[Route('/mon-compte/newsletter/inscription', name: 'app_account_newsletter_subscribe', methods: ['POST'])]
    public function subscribeToNewsletter(EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $email = $user->getEmail();

        

        // Vérifie s'il est déjà inscrit
        $existing = $em->getRepository(Newsletter::class)->findOneBy(['email' => $email]);

        if (!$existing) {
            $newsletter = new Newsletter();
            $newsletter->setEmail($email);
            $em->persist($newsletter);
            $em->flush();

            $this->addFlash('success', 'Vous êtes maintenant inscrit à la newsletter.');
        }

        return $this->redirectToRoute('app_account');
    }

    // Se désinscrire de la newsletter
    #[Route('/mon-compte/newsletter/desinscription', name: 'app_account_newsletter_unsubscribe', methods: ['POST'])]
    public function unsubscribeFromNewsletter(EntityManagerInterface $em, NewsletterRepository $newsletterRepo): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $email = $user->getEmail();

        $existing = $newsletterRepo->findOneBy(['email' => $email]);

        if ($existing) {
            $em->remove($existing);
            $em->flush();
            $this->addFlash('success', 'Vous avez été désinscrit de la newsletter.');
        }

        return $this->redirectToRoute('app_account');
    }

    #[Route('/mon-compte/adresse-livraison/ajouter', name: 'app_account_shipping_adress_add')]
    
    public function addShippingAdress(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $shippingAdress = new ShippingAdress();
        $form = $this->createForm(ShippingAdressType::class, $shippingAdress);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On lie l'adresse à l'utilisateur
            $shippingAdress->setUser($user);

            $em->persist($shippingAdress);
            $em->flush();

            $this->addFlash('success', 'Votre adresse de livraison a été ajoutée avec succès.');
            return $this->redirectToRoute('app_account'); // Redirige vers la page "Mon compte"
        }

        return $this->render('account/add_shipping_adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/adresse-livraison/{id}/modifier', name: 'app_account_shipping_adress_edit')]
    public function editShippingAdress(Request $request, EntityManagerInterface $entityManager, ShippingAdress $shippingAdress): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($shippingAdress->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Accès interdit.');
        }

        $form = $this->createForm(ShippingAdressType::class, $shippingAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre adresse de livraison a été mise à jour.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit_shipping_adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/adresse-livraison/{id}/supprimer', name: 'app_account_shipping_adress_delete', methods: ['POST'])]
    public function deleteShippingAdress(int $id, ShippingAdress $shippingAdress, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifie si l'adresse existe et si elle appartient à l'utilisateur connecté
        if (!$shippingAdress || $shippingAdress->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer cette adresse.");
        }

        // Vérifie le jeton CSRF
        if ($this->isCsrfTokenValid('delete_shipping_adress_' . $shippingAdress->getId(), $request->request->get('_token'))) {
            $em->remove($shippingAdress);
            $em->flush();

            $this->addFlash('success', 'Adresse supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_account');
    }

    


    #[Route('/mon-compte/adresse-facturation/ajouter', name: 'app_account_billing_adress_add')]
    
    public function addBillingAdress(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $billingAdress = new BillingAdress();
        $form = $this->createForm(BillingAdressType::class, $billingAdress);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On lie l'adresse à l'utilisateur
            $billingAdress->setUser($user);

            $em->persist($billingAdress);
            $em->flush();

            $this->addFlash('success', 'Votre adresse de facturation a été ajoutée avec succès.');
            return $this->redirectToRoute('app_account'); // Redirige vers la page "Mon compte"
        }

        return $this->render('account/add_billing_adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/compte/adresse-facturation/modifier', name: 'app_account_billing_adress_edit')]
    public function editBillingAdress(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $billingAdress = $user->getBillingAdress(); // récupère l'adresse liée au user

        if (!$billingAdress) {
            // Si l'utilisateur n'a pas encore d'adresse, on en crée une
            $billingAdress = new BillingAdress();
            $billingAdress->setUser($user);
            $entityManager->persist($billingAdress);
        }

        $form = $this->createForm(BillingAdressType::class, $billingAdress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse de facturation a été mise à jour.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit_billing_adress.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/mon-compte/adresse-facturation/{id}/supprimer', name: 'app_account_billing_adress_delete', methods: ['POST'])]
    public function deleteBillingAdress(int $id, BillingAdress $billingAdress, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        // Vérifie si l'adresse existe et si elle appartient à l'utilisateur connecté
        if (!$billingAdress || $billingAdress->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer cette adresse.");
        }

        // Vérifie le jeton CSRF
        if ($this->isCsrfTokenValid('delete_billing_adress_' . $billingAdress->getId(), $request->request->get('_token'))) {
            $em->remove($billingAdress);
            $em->flush();

            $this->addFlash('success', 'Adresse supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_account');
    }

    #[Route('/mon-compte/mes-commandes', name: 'app_account_orders')]
    public function orders(OrderRepository $orderRepository, Security $security): Response
    {
        $user = $security->getUser();
        $orders = $orderRepository->findBy(['user' => $user]);
    
        return $this->render('account/orders.html.twig', [
            'orders' => $orders,
        ]);
    }
    

}

