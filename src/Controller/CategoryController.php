<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CategoryController extends AbstractController
{
    #[Route('/{category}/edit', name: 'category_edit')]
    public function edit(
        Request $request,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $em // 
    ): Response {
        $category = $categoryRepository->findOneBy([
            'name' => $request->get('category')
        ]);

        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('image')->getData()) {
                $imageFile = $form->get('image')->getData();
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $category->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de votre image');
                }
            }

            $category->setName($form->get('name')->getData());
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category', [
                'category' => $category->getName()
            ]);
        }
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'editForm' => $form
        ]);
    }
    #[Route('/new-category', name: 'category_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        return $this->render('category/new.html.twig', [
        ]);
    }
}
