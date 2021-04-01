<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaylistController extends AbstractController
{
    /**
     * @Route("/playlist", name="playlist")
     */
    public function index(): Response
    {
        dd($this->render('playlist/index.html.twig', [
            'controller_name' => 'PlaylistController',
            'GET',
            'https://api.spotify.com',
            'https://accounts.spotify.com'
        ]));
    }
}
