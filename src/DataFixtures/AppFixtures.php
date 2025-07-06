<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Material;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        
    }
    public function load(ObjectManager $manager): void
    {

        $categories = ['bague', 'boucle', 'bracelet', 'montre', 'collier'];

        $categoriesObjects = [];
     
        foreach ($categories as $nomCategorie) {
            $category = new Category();
            $category->setName($nomCategorie);
            $manager->persist($category);
            $categoriesObjects[] = $category;
        }


        $marques = ['dior', 'channel', 'louis vuitton', 'festina', 'rolex'];

        $marquesObjects = [];

        foreach ($marques as $nomMarque) {
            $brand = new Brand();
            $brand->setName($nomMarque);
            $manager->persist($brand);
            $marquesObjects[] = $brand;
        }

        $matieres = ['or', 'argent', 'cuir', 'perle', 'diamant'];

        $matieresObjects = [];

        foreach ($matieres as $nomMatiere) {
            $material = new Material();
            $material->setName($nomMatiere);
            $manager->persist($material);
            $matieresObjects[] = $material;
        }

        $descriptions = [
            'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Eos, atque', 
            'Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta dolorem iure labore odio, id error nam neque repellat temporibus dolor', 
            'Lorem, ipsum dolor sit amet consectetur adipisicing elit. Accusamus alias libero quam inventore nihil, consequatur vero suscipit eaque iure recusandae incidunt hic error iusto mollitia. Delectus magnam, temporibus nobis eligendi asperiores, voluptatem illum quae blanditiis saepe nam totam debitis repellat reiciendis odio quod qui ab odit dicta, aliquam distinctio quo', 
            null
        ];

        // products

        

        for ($i=1; $i <= 100 ; $i++) { 

            $lastKeyCategorie = array_key_last($categories);
            $keyAleatoireCategory = rand(0, $lastKeyCategorie);

            $lastKeyMatiere = array_key_last($matieres);
            $keyAleatoireMatiere = rand(0, $lastKeyMatiere);

            $price = rand(100,2000);

            $lastKeyDescription = array_key_last($descriptions);
            $keyAleatoireDescription = rand(0, $lastKeyDescription);


            $lastKeyCategoryObject = array_key_last($categoriesObjects);
            $keyAleatoireCategoryObject = rand(0, $lastKeyCategoryObject);

            $lastKeyMarqueObject = array_key_last($marquesObjects);
            $keyAleatoireMarqueObject = rand(0, $lastKeyMarqueObject);


            $matieresProduct = [];
            $lastKeyMatiereObject = array_key_last($matieresObjects);
            $keyAleatoireMatiereObject = rand(0, $lastKeyMatiereObject);

           $product = new Product();
           $product->setTitle($categories[$keyAleatoireCategory] . ' en ' . $matieres[$keyAleatoireMatiere]);
           $product->setPrice($price);
           $product->setDescription($descriptions[$keyAleatoireDescription]);
           $product->setCategory($categoriesObjects[$keyAleatoireCategoryObject]);
           $product->setBrand($marquesObjects[$keyAleatoireMarqueObject]);
           $product->addMaterial($matieresObjects[$keyAleatoireMatiereObject]);

            //    $countMatieres = count($matieresObjects);
            //    $keyaleatoire = rand(1, $countMatieres);
            //    for ($i=1; $i <=  $keyaleatoire; $i++) { 
            //     $material = $matieresObjects[$keyAleatoireMatiereObject];

            //     if (in_array($material, $product->getMaterials())) {
            //         # code...
            //     }
            //     $product->addMaterial($matieresObjects[$keyAleatoireMatiereObject]);
            //    }
           
            $manager->persist($product);
        }


        // user

        $prenoms = ['jean', 'bart', 'ugo', 'marie', 'pierre'];
        $noms = ['dupont', 'martin', 'lord', 'durant'];

        $roles = ['ROLE_ADMIN', 'ROLE_MODO', 'ROLE_INTERN'];


        // $user = new User();
        // $user->setEmail();
        // $user->setLastName();
        // $user->setFirstName();
        // $user->setRoles();
        // $user->setPassword($this->userPasswordHasher->hashPassword($user, 123456));
        // $manager->persist($user);

        for ($i=1; $i <= 50; $i++) { 

            $lastKeyPrenom = array_key_last($prenoms);
            $keyAleatoirePrenom = rand(0, $lastKeyPrenom);

            $lastKeyNom = array_key_last($noms);
            $keyAleatoireNom = rand(0, $lastKeyNom);

            $lastKeyRole = array_key_last($roles);
            $keyAleatoireRole = rand(0, $lastKeyRole);


            $user = new User();
            $user->setEmail($prenoms[$keyAleatoirePrenom] . '.' . $noms[$keyAleatoireNom] . '.' . $i . '@gmail.com');
            $user->setLastName($noms[$keyAleatoireNom]);
            $user->setFirstName($prenoms[$keyAleatoirePrenom]);
            $user->setRoles([$roles[$keyAleatoireRole]]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 123456));
            $manager->persist($user);
        }

        $manager->flush();
    }
}

