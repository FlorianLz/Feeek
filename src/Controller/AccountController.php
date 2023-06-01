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
        $isAdministrator = $this->isGranted('ROLE_ADMIN');
        if ($isAdministrator) {
            $posts = $this->postRepository->findAll();
        } else{
            $posts = $this->postRepository->findBy(['author' => $this->getUser()]);
        }

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'posts' => $posts
        ]);
    }
}
