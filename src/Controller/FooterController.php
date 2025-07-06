<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterController extends AbstractController
{
    #[Route('/mentions-legales', name:'app_legal_notices')]
    public function legalNotices(): Response
    {
        return $this->render('legal_notices.html.twig', [
            'title' => 'Mentions légales'
        ]);
    }

    


}
