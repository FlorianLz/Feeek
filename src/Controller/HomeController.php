<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private readonly \App\Repository\PostRepository $postRepository
    ) {
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        //Get all posts
        $posts = $this->postRepository->findAll();
        //Randomize posts
        shuffle($posts);
        $connectedUser = $this->getUser();
        $favorites = $connectedUser?->getFavorites();
        $tabFavorites = [];
        if($favorites){
            foreach($favorites as $favorite){
                $tabFavorites[] = $favorite->getPostId()->getId();
            }
        }
        return $this->render('home/index.html.twig', [
            "posts" => $posts,
            "connectedUser" => $connectedUser?->getId(),
            "favorites" => $tabFavorites
        ]);
    }
}