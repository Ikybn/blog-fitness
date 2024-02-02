<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PostController extends AbstractController
{
    #[Route('/posts', name: 'app_post_show')]
    public function show(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/show.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/post/new', name: 'app_post_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }
        return $this->render('post/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/post/{slug}/update', name: 'app_post_update')]
    public function update(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    #[Route('/post/{slug}/delete', name: 'app_post_delete')]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response
    {

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->get('_token'))) {
            $em->remove($post);
            $em->flush();
        }
        return $this->redirectToRoute('app_post_show');
    }
}