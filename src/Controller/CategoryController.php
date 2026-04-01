<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategoryFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategorieRepository $repo, ): Response
    {

        $categories = $repo->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $entityManager, Request $request): Response
    {
        $category = new Categorie();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Votre catégorie a bien été créée'); 
        }

        return $this->render('category/newCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/category/update/{id}', name: 'app_category_update')]
    public function editCategory(Categorie $category, EntityManagerInterface $up, Request $request): Response
    { // Categorie $category POUR aller chercher la class categorie dont l'id est {n°}, $category correspond à l'objet complet
    // Pas une nouvelle instanciation (au sens new Categorie()), c'est une récupération

    // Request = info validées par l'utilisateur (voix de l'utilisateur) / $request = contenu de toutes les infos envoyées par l'utilisateur 
        
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $up->flush();

            $this->addFlash('info', 'Votre catégorie a bien été modifiée'); 

            return $this->redirectToRoute('app_category'); // Change de page pour retourner à app_category (les updates se font 1 par 1)
        }

        return $this->render('category/updateCategory.html.twig', [ // affiche le formulaire
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/category/delete{id}', name: 'app_category_delete')] //! CATEGORY DELETE
    public function deleteCategory(Categorie $category, EntityManagerInterface $entityManager): Response
    {
            $entityManager->remove($category);
            $entityManager->flush();

        $this->addFlash('danger', 'Votre catégorie a bien été supprimée'); 

        return $this->redirectToRoute('app_category');
    }

}