<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategorieRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
// {
//     #[Route('/', name: 'app_home_page')]
//     public function index(): Response
//     {
//         $nomstudents=['Jérmie', 'Léa', 'Pierick', 'Davy'];
//         $age =17;

//         return $this->render('home_page/index.html.twig', [
//             // 'controller_name' => 'HomePageController',
//             'lesNoms' => $nomstudents,
//             'age' => $age
//         ]);
//     }
// }
{
#[Route('/', name: 'app_home_page', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('home_page/index.html.twig', [
            'products' => $productRepository->findAll(),
            'categories' => $categorieRepository->findAll()
        ]);
    }

    #[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct(Product $product, ProductRepository $productRepository, CategorieRepository $categorieRepository): Response 
    {
        // On récupère les 5 derniers produits par ID décroissant
        $lastProductsAdd = $productRepository->findBy([], ['id' => 'DESC'], 5); 

        return $this->render('home_page/show.html.twig', [ 
            'product' => $product,
            'products' => $lastProductsAdd,
            'categories' => $categorieRepository->findAll()
        ]);
    }
}