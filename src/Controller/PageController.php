<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    #[Route('/page', name: 'home', methods: ['GET'])]
    public function index(
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
    ): Response {
        return $this->render('page/home.html.twig', [
            'posts' => $postRepository->findAll(),
            'categories'=> $categoryRepository->findAll(),
        ]);
    }

    #[Route('/{category}', name: 'category', methods: ['GET'])]
    public function category(
        Request $request,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
        ): Response {
            $category = $categoryRepository->findOneBy([
                'name'=> $request->get('category')
            ]);
            return $this->render('page/category.html.twig', [
                'categories'=> $category,
                'posts' => $postRepository->findBy(['category'=>$category]),
            ]);
}
}