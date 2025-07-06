<?php

namespace App\Controller; // App correspond à 'src'

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\CommentType;
use App\Service\WishlistService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController // héritage de AbstractController
{

    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('home/index.html.twig', [
            
        ]);
    }


    #[Route('/catalogue', name:'app_catalog')]
    // #[IsGranted('ROLE_ADMIN')]
    public function catalog(ProductRepository $productRepository, WishlistService $wishlistService, Security $security): Response
    {
        $products = $productRepository->findAll();
        $user = $security->getUser();

        $wishlistProductIds = [];

        if ($user) {
            $wishlistProducts = $wishlistService->getUserWishlistProducts($user);
            $wishlistProductIds = array_map(fn($product) => $product->getId(), $wishlistProducts);
        }
        return $this->render('home/catalog.html.twig', [
            'products' => $products,
            'wishlistProductIds' => $wishlistProductIds,
        ]);
    }

    #[Route('/catalogue/categorie/{id}', name: 'catalogue_category')]
public function catalogueByCategory(Category $category, ProductRepository $productRepository, WishlistService $wishlistService, Security $security): Response
{
    $products = $productRepository->findBy(['category' => $category]);
    $user = $security->getUser();

    $wishlistProductIds = [];

     if ($user) {
            $wishlistProducts = $wishlistService->getUserWishlistProducts($user);
            $wishlistProductIds = array_map(fn($product) => $product->getId(), $wishlistProducts);
        }

    return $this->render('home/catalog.html.twig', [
        'products' => $products,
        'category' => $category,
        'wishlistProductIds' => $wishlistProductIds,
    ]);
}


    #[Route('/produit/{id}', name:'app_catalog_product')]
    public function productFiche(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() AND $form->isValid()) {
            
            $comment->setUser($this->getUser());
            $comment->setProduct($product);
            $comment->setCreatedAt(new \DateTimeImmutable('now'));

            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Merci pour votre commentaire, il sera traîté dans les plus brefs délais');
            $this->redirectToRoute('app_catalog_product', ['id' => $product->getId()]);
        }


        return $this->render('home/product.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    

}
