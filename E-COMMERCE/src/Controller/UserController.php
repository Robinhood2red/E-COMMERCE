<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/user/{id}/to/editor', name: 'app_user_to_editor')] //! USER TO EDITOR
    public function changeRole(User $user, EntityManagerInterface $entityManager): Response
    {
            $user->setRoles(['ROLE_EDITOR', 'ROLE_USER']);
            $entityManager->flush();

        $this->addFlash('success', "Le rôle éditeur à bien été ajouté à l'utilisateur"); 
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove/editor/role', name: 'app_user_remove_editor_role')] //! REMOVE EDITOR
    public function removeRoleeditor(User $user, EntityManagerInterface $entityManager): Response
    {
            $user->setRoles([]);
            $entityManager->flush();

        $this->addFlash('danger', "Le rôle éditeur à bien été retiré à l'utilisateur"); 
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/delete', name: 'app_user_delete')] //! DELETE EDITEUR
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur a bien été supprimé."); 
        return $this->redirectToRoute('app_user'); 
    }
}

