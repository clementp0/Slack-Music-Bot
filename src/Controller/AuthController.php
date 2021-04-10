<?php

namespace App\Controller;

use App\Entity\User;
use SpotifyWebAPI\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session as RequestSpotify;
use SpotifyWebAPI\SpotifyWebAPI;
use GuzzleHttp\Client;

class AuthController extends AbstractController
{
    private $spotifyParams;
    private $spotify;

    public function __construct()
    {
        $this->spotifyParams = [
            'client_id' => 'ead4f33a4b734677a53d154955729f10',
            'client_secret' => '37fc188e841741ca8ea02228ab1b9407',
            'scope' => [
                'user-read-email', 'user-read-private', 'playlist-read-private',
                'playlist-read-collaborative', 'playlist-modify-public',
                'playlist-modify-private', 'user-follow-read', 'user-follow-modify'
            ]
        ];

        $this->spotify = new Session(
            $this->spotifyParams['client_id'],
            $this->spotifyParams['client_secret'],
            'http://127.0.0.1:8000/login/oauth'
        );
    }
    // src/Controller/AuthController
    /**
     * @Route("/login", name="login")
     */
    public function login(SessionInterface $session)
    {

        $options = [
            'scope' => $this->spotifyParams['scope']
        ];

        $spotify_auth_url = $this->spotify->getAuthorizeUrl($options);

        return $this->render('auth/login.html.twig', array(
            'spotify_auth_url' => $spotify_auth_url
        ));
    }

    // src/Controller/AuthController

    /**
     * @Route("/login/oauth", name="oauth")
     */
    public function oauth(Request $request, SessionInterface $session)
    {

        $accessCode = $request->get('code');
        $session->set('accessCode', $accessCode); // symfony session

        $this->spotify->requestAccessToken($accessCode);
        $accessToken = $this->spotify->getAccessToken();
        $session->set('accessToken', $accessToken); // symfony session

        //récupère les datas grâce au token
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        $me = $api->me();
        //envoi les datas vers la bdd

        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName($me->display_name);
        $user->setToken($accessToken);

        $entityManager->persist($user);

        $entityManager->flush();

        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(RequestSpotify $request)
    {
        // $accessToken = $request->get('accessToken');

        $accessToken = 0;

        if (!$accessToken) {
            $request->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }



        // $api = new SpotifyWebAPI();
        // $api->setAccessToken($accessToken);
        // $me = $api->me();

        $me = 0;

        $client = new Client();
        $res = $client->get('https://api.spotify.com/v1/me/playlists', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' =>  'Bearer ' . $accessToken
            ]
        ]);

        return $this->render('auth/profile.html.twig', array(
            'me' => $me,
            'token' => $accessToken,
            'request' => json_decode($res->getBody()->getContents())
        ));
    }
    // src/Controller/AuthController

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(RequestSpotify $request)
    {
        $request->clear();
        $request->getFlashBag()->add('success', 'You have successfully logged out');

        return $this->redirectToRoute('login');
    }
}
