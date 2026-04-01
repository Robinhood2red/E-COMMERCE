<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['POST'])]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        // On récupère la valeur du name="word" envoyé en POST
        $word = $request->request->get('word');

        // On vérifie si la méthode est bien POST et si on a un mot
        if ($request->isMethod('POST') && $word) {
            
            // dd($word); 

            $results = $productRepository->searchEngine($word);
            // dd($results, $word);
            // dd($results);

            return $this->render('search/index.html.twig', [
                'products' => $results,
                'word' => $word // Pour afficher "Résultats pour : Nike" par exemple
            ]);
            
        }

        // Si on arrive ici sans POST, on redirige vers l'accueil par exemple
        return $this->redirectToRoute('app_home_page');
    }
}