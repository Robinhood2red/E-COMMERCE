<?php

namespace App\Controller;

use App\Entity\Product;
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
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home_page/index.html.twig', [
            'products'=>$productRepository->findAll()
        ]);
    }

#[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function showProduct(Product $product): Response 
    {
        return $this->render('home_page/show.html.twig', [ 
            'product' => $product
        ]);
    }
}