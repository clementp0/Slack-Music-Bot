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
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

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
            'https://smb.clpo.net/login/oauth'
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
        $response = new Response();
        $cookie = new Cookie('uis',$_GET['uis'], strtotime('tomorrow'));
        $response->headers->setCookie($cookie);
        $response->send();
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
        $idUserSlack = $_COOKIE["uis"];
        $entityManager = $this->getDoctrine()->getManager();
        
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'id_user_slack' => $idUserSlack
            ]);


        $accessToken = '';    
        if($user !== null && false) // TODO: vérifier la validité du token : remplacer le false par la validation du token
            $accessToken = $user->getToken();
        
        if(empty($accessToken)) {
            $accessCode = $request->get('code');
            $this->spotify->requestAccessToken($accessCode);
            $accessToken = $this->spotify->getAccessToken();
            // $session->set('accessCode', $accessCode); // symfony session
            // $session->set('accessToken', $accessToken); // symfony session
            $cookie = new Cookie('accessToken', $accessToken, strtotime('tomorrow'));
            $response->headers->setCookie($cookie);
            $response->send();
        }

        //récupère les datas grâce au token
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        
        if($user === null) {
            $me = $api->me();
            $user = new User();
            $user->setName($me->display_name);
            $user->setToken($accessToken);
            $user->setIdUserSlack($idUserSlack);
        } else {
            $user->setToken($accessToken);
        }
        //envoi les datas vers la bdd
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('profile');
    }

    /**
     * @Route("/test", name="test")
     */
    public function test() {
        $idUserSlack = $_COOKIE["uis"];

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'id_user_slack' => $idUserSlack
                ]);

        var_dump($user);die;
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile()
    {
        $accessToken = $_COOKIE['accessToken'];
    
        if (!$accessToken) {
            // $request->getFlashBag()->add('error', 'Invalid authorization');
            $this->redirectToRoute('login');
        }

        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);

        $client = new Client();
        $res = $client->get('https://api.spotify.com/v1/me/playlists', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' =>  'Bearer ' . $accessToken
            ]
        ]);

        $me = $api->me();

        return $this->render('auth/profile.html.twig', array(
            'me' => $me,
            'token' => $accessToken,
            'request' => json_decode($res->getBody()->getContents())->items
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
