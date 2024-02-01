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
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
    ): Response {
        $posts = $postRepository->findAll();
        $categories= $categoryRepository->findAll();

        return $this->render('page/home.html.twig', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    #[Route('/{category}', name: 'category', methods: ['GET'])]
    public function category(
        Request $request,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
        ): Response {
            $categoryName = $request -> get('category');
            $category = $categoryRepository->findOneBy([
                'name'=> $categoryName]);
         if (!$category) {
                    throw $this->createNotFoundException('La catégorie n\'existe pas.');
                }
        
                $posts = $postRepository->findBy(['category' => $category]);
        
            return $this->render('page/category.html.twig', [
                'category'=> $category,
                'posts' => $postRepository->findBy(['category'=>$category]),
            ]);
}
}