<?php

namespace App\Controller;

use App\Repository\FavoriteRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    private FavoriteRepository $favoriteRepository;
    private PostRepository $postRepository;

    public function __construct(FavoriteRepository $favoriteRepository, PostRepository $postRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->postRepository = $postRepository;


    }
    #[Route('/favorite/toggle', name: 'app_favorite')]
    public function toggleFavorite(Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $data = json_decode( $request->getContent(), true);
        $idPost = $data['idPost'];

        $post = $this->postRepository->find($idPost);

        $this->favoriteRepository->toggleFavorite($user, $post);
        return new Response(json_encode(['success' => true]));
    }

    #[Route('/favorites', name: 'app_favorites_home')]
    public function favorites(Request $request): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $connectedUser = $this->getUser();
        $favorites = $this->postRepository->findAllFavorites($connectedUser);

        return $this->render('favorite/index.html.twig', [
            'posts' => $favorites,
            'connectedUser' => $connectedUser
        ]);
    }
}
