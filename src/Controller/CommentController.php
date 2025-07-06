<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Comment1Type;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class CommentController extends AbstractController
{
    #[Route('/admin/commentaire', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findBy(['activation' => false]),
            // SELECT * FROM comment WHERE activation = false
        ]);
    }

    #[Route('/commentaire/mes-commentaires', name: 'app_comment_mine', methods: ['GET'])]
    public function myComments(CommentRepository $commentRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $comments = $commentRepository->findBy(['user' => $user]);

        return $this->render('comment/my_comments.html.twig', [
            'comments' => $comments
        ]);
    }



    #[Route('commentaire/modifier/{id}', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setActivation(true);
        $entityManager->flush();
        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('commentaire/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
