<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\AlphaCampRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
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
    #[Route('/product/subcategory/{id}/filter', name: 'app_home_product_filter', methods: ['GET'])]
    public function filter(int $id, AlphaCampRepository $alphaCampRepository, CategorieRepository $categorieRepository): Response {
        // ici pour récupèrer la sous-catégorie
        $alphaCamp = $alphaCampRepository->find($id);

        if (!$alphaCamp) {
            throw $this->createNotFoundException('Le secteur Alpha spécifié est introuvable.');
        }

        // Relation directe depuis l'objet AlphaCamp
        $products = $alphaCamp->getProducts();

        return $this->render('home_page/filter.html.twig', [
            'products' => $products,
            'categories' => $categorieRepository->findAll(),
            'current_filter' => $alphaCamp->getName()
        ]);
    }
}