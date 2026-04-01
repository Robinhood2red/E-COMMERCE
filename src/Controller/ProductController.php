<?php

namespace App\Controller;

use App\Entity\AddProductHistory;
use App\Entity\Product;
use App\Form\AddProductHistoryType;
use App\Form\ProductType;
use App\Form\ProductUpdateType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\AlphaCampRepository;
use App\Repository\CategorieRepository;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/editor')]
#[IsGranted('ROLE_ADMIN')]
final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
#region NEW
    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();//! on recup l'image et son contenu
   
            if ($image) {/*si l'image existe*/
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME); // Nom d'origine de l'image
                $safeImageName = $slugger->slug($originalName);/* permet de recup des image avec espace dans le nom et l'enlever*/
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();/*cree un id unique a toute les images meme si elles ont un nom similaire*/

                try { // On tente de déplacer le fichier physiquement sur le serveur
                    $image->move
                        ($this->getParameter('image_directory'), // getParameter, crée un dossier et envoie le à cet endroit là ('dans services.yaml')
                        $newFileImageName);/* on recup l'image et on la renomme et on la stocke dans le repoertoire */
                }catch (FileException $exception) {}/*en cas d'erreur -> Si le déplacement échoue, on arrive ici*/
                    $product->setImages($newFileImageName); // set(nouveau nom image)
                
            }

            $entityManager->persist($product); // 
            $entityManager->flush();

            $stockHistory = new AddProductHistory();/*nouvelle instanciation de la classe*/
            $stockHistory->setQuantity($product->getStock());/*on recup l'id du produit et on ajoute au stock*/
            $stockHistory->setProduct($product);/*on insere le produit*/
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();/*effectue la mise a jour en bdd*/
            
            $this->addFlash('success','Votre produit a été ajouté');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
#endregion 
#region SHOW
    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product, CategorieRepository $categorieRepository): Response {
    
    // récupère la collection des sous-catégories via la méthode exacte de votre entité
    $subCategories = $product->getSubCategory();

    // Le nom à afficher pour le current_filter
    $currentFilter = "Détails Unité";
    
    if (!$subCategories->isEmpty()) {
        $currentFilter = $subCategories->first()->getName();
    }

    return $this->render('product/show.html.twig', [
        'product' => $product,
        'categories' => $categorieRepository->findAll(),
        'current_filter' => $currentFilter,
    ]);
}
#endregion
#region EDIT
    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(SluggerInterface $slugger, Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();//! on recup l'image et son contenu
                if ($image) {/*si l'image existe*/
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME); // Nom d'origine de l'image
                $safeImageName = $slugger->slug($originalName);/* permet de recup des image avec espace dans le nom et l'enlever*/
                $newFileImageName = $safeImageName.'-'.uniqid().'.'.$image->guessExtension();/*cree un id unique a toute les images meme si elles ont un nom similaire*/

                try { // On tente de déplacer le fichier physiquement sur le serveur
                    $image->move
                        ($this->getParameter('image_directory'), // getParameter, crée un dossier et envoie le à cet endroit là ('dans services.yaml')
                        $newFileImageName);/* on recup l'image et on la renomme et on la stocke dans le repoertoire */
                }catch (FileException $exception) {}/*en cas d'erreur -> Si le déplacement échoue, on arrive ici*/
                    $product->setImages($newFileImageName); // set(nouveau nom image)
            } 
            $entityManager->flush();
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
#endregion
#region ADD
#[Route('/add/product/{id}/', name: 'app_product_stock_add', methods: ['POST','GET'])]
public function stockAdd($id, Request $request, EntityManagerInterface $entityManager): Response
{
    $product = $entityManager->getRepository(Product::class)->find($id);

    $stockAdd = new AddProductHistory();
    $form = $this->createForm(AddProductHistoryType::class, $stockAdd);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if($stockAdd->getQuantity()>0){ // Aditions seulement
            // Pour voi la quantitée actuelle
            $newQuantity = $stockAdd->getQuantity();

            // pn met à jour le stock du produit
            $currentStock = $product->getStock();
            $product->setStock($currentStock + $newQuantity);

            //* Ajoute la date de l'ajout du produit
            // AddProductHistory NOT NULL obligatoire --> sans setCreatedAt la colonne created_at sera vide
            $stockAdd->setCreatedAt(new \DateTimeImmutable()); 
            // Sans setProduct la colonne product_id (la clé étrangère) sera vide
            $stockAdd->setProduct($product); 

            $entityManager->persist($stockAdd);
            $entityManager->flush();

            $this->addFlash('success', 'Le stock a été mis à jour avec succès !');

            return $this->redirectToRoute('app_product_index');
        }
    }

    return $this->render('product/addStock.html.twig', [
        'form' => $form->createView(),
        'product' => $product
    ]);
}
#endregion
#region HISTO REAPRO
#[Route('/add/product/{id}/stock/history', name: 'app_product_stock_add_history', methods: ['GET'])]
    public function showHistoryProductStock($id, ProductRepository $productRepository, AddProductHistoryRepository $addProductHistoryRepository):Response
    {
        //* On utilise le Repository de Product pour transformer l'ID de l'URL en un objet complet
        // Cela permet de vérifier si le produit existe et de récupérer son Nom, son Stock actuel
        $product = $productRepository->find($id);// ici pour récup le produit en paramètres

        //* On utilise le Repository de l'historique pour chercher les lignes spécifiques
        // - ['product' => $product] : On filtre pour n'avoir QUE l'historique lié à ce produit (le WHERE en SQL)
        // - ['id' => 'DESC'] : On trie par ID décroissant pour avoir les ajouts les plus récents en haut (le ORDER BY)
        $productAddHistory = $addProductHistoryRepository->findBy(['product'=>$product],['id'=>'DESC']);
        
        //* On envoie les données à la vue Twig
        // "productsAdded" est le nom de la variable dans la boucle {% for %} en Twig
        // "product" est passé ici pour afficher le nom du produit dans le titre de la page
        return $this->render('product/addedHistoryStockShow.html.twig',[ // 
            "productsAdded"=>$productAddHistory
        ]);
    }
#endregion
#region DELETE
    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
#endregion