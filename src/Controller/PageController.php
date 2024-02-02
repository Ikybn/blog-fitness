<?php

namespace App\Controller;

use App\Form\RegisterType;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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

//     #[Route('/{category}', name: 'category', methods: ['GET'])]
//     public function category(
//         Request $request,
//         CategoryRepository $categoryRepository,
//         PostRepository $postRepository,
//         ): Response {
//             $categoryName = $request -> get('category');
//             $category = $categoryRepository->findOneBy([
//                 'name'=> $categoryName]);
//          if (!$category) {
//                     throw $this->createNotFoundException('La catÃ©gorie n\'existe pas.');
//                 }
      
//                 $posts = $postRepository->findBy(['category' => $category]);
      
//             return $this->render('page/category.html.twig', [
//                 'category'=> $category,
//                 'posts' => $postRepository->findBy(['category'=>$category]),
//             ]);
// }
#[Route("/register", name: "app_register")]
public function register(Request $request, EntityManagerInterface $em): Response
{
    $form = $this->createForm(RegisterType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $form->getData();

        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Inscription réussie! Connectez-vous maintenant.');

        return $this->redirectToRoute('/');
    }

    return $this->render('registration/register.html.twig', [
        'form' => $form->createView(),
    ]);
}

}