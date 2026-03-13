<?php

namespace App\Controller;

use App\Entity\AlphaCamp;
use App\Form\AlphaCampType;
use App\Repository\AlphaCampRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/alpha/camp')]
#[IsGranted('ROLE_ADMIN')]
final class AlphaCampController extends AbstractController
{
    #[Route(name: 'app_alpha_camp_index', methods: ['GET'])]
    public function index(AlphaCampRepository $alphaCampRepository): Response
    {
        return $this->render('alpha_camp/index.html.twig', [
            'alpha_camps' => $alphaCampRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_alpha_camp_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $alphaCamp = new AlphaCamp();
        $form = $this->createForm(AlphaCampType::class, $alphaCamp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($alphaCamp);
            $entityManager->flush();

            return $this->redirectToRoute('app_alpha_camp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alpha_camp/new.html.twig', [
            'alpha_camp' => $alphaCamp,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_alpha_camp_show', methods: ['GET'])]
    public function show(AlphaCamp $alphaCamp): Response
    {
        return $this->render('alpha_camp/show.html.twig', [
            'alpha_camp' => $alphaCamp,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_alpha_camp_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AlphaCamp $alphaCamp, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AlphaCampType::class, $alphaCamp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_alpha_camp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('alpha_camp/edit.html.twig', [
            'alpha_camp' => $alphaCamp,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_alpha_camp_delete', methods: ['POST'])]
    public function delete(Request $request, AlphaCamp $alphaCamp, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$alphaCamp->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($alphaCamp);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_alpha_camp_index', [], Response::HTTP_SEE_OTHER);
    }
}
