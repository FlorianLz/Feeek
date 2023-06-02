<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    function __construct(private PostRepository $postRepository)
    {}

    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $isAdministrator = $this->isGranted('ROLE_ADMIN');
        $posts = $this->postRepository->findAll();

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'posts' => $posts,
            'isAdmin' => $isAdministrator
        ]);
    }
}
