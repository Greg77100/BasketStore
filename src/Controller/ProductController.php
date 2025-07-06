<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/produit')]

final class ProductController extends AbstractController
{

    #[Route('/afficher', name:'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
         

        $products = $productRepository->findAll(); 
        
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/ajouter', name:'app_product_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        
        $product = new Product();

        
        
        $form = $this->createForm(ProductType::class, $product);

       
       
        $form->handleRequest($request);

       
        if ($form->isSubmitted() && $form->isValid()) {
            
            $pictureFile = $form->get('picture')->getData();

            
            if ($pictureFile) {

                
                $pictureFileName = date('YmdHis') . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                 
 
                    $pictureFile->move(
                        $this->getParameter('picture_parameter'),
                        $pictureFileName
                    );
                    
                
                    $product->setPicture($pictureFileName);
            }

           
          
            $em->persist($product); 
            $em->flush();
          

           
            $this->addFlash('success', 'Le produit a bien été ajouté');
          
            return $this->redirectToRoute('app_product_index');
        }


        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView(), 
        ]);
    }

    #[Route('/fiche/{id}', name:'app_product_show')]
    public function show(Product $product): Response
    {
        

        return $this->render('product/show.html.twig',[
            'product' => $product
        ]);
    }

    #[Route('/modifier/{id}', name:'app_product_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pictureFile = $form->get('picture')->getData();

        if ($pictureFile) {
            // On crée un nom de fichier propre
            $pictureFileName = date('YmdHis') . '-' . uniqid() . '.' . $pictureFile->guessExtension();

            // On déplace l'image dans public/image/product/
            $pictureFile->move(
                $this->getParameter('picture_parameter'),
                $pictureFileName
            );

            // On stocke le nom du fichier seulement (pas le chemin complet)
            $product->setPicture($pictureFileName);
        }


            $em->flush();
            $this->addFlash('success', 'Le produit a bien été modifié');
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form->createView()
        ]);
       
    }
    
    #[Route('/supprimer/{id}', name:'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $em): Response
    {
        $em->remove($product);
        $em->flush();
        $this->addFlash('success', 'Le produit a bien été supprimé');
        return $this->redirectToRoute('app_product_index');
    }
    


}
