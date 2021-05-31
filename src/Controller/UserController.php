<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use SpotifyWebAPI\SpotifyWebAPI;
use GuzzleHttp\Client;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Request $request)
    {
//        $message = "Hey <@" . $request->getContent() . ">, how is it going ?";
//        $struct = [
//            "blocks" =>
//            [
//                [
 //                   "type" => "section", "text" => ["type" => "mrkdwn", "text" => $message]
  //              ]
   //         ]
    //    ];
     //   return (new JsonResponse(json_decode($request->getContent())));

    
     // Vérifier si le token existe (+ validation)

        // Si le token existe
            //Aller vers la méthode correspondant à la commande tapée (ex: playlist)
        // Sinon
            // Retourner le lien pour s'authentifier

            
        $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->findOneBy([
            'id_user_slack' => $request->request->get('user_id')
            ]);

        if(false) {
            $message = "https://smb.clpo.net/login?uis=" . $request->request->get('user_id');
            
            $struct = [
                "blocks" =>
                [
                    [
                        "type" => "section", "text" => ["type" => "mrkdwn", "text" => $message]
                    ]
                ]
            ];
            return (new JsonResponse($struct));
        } else {
            $token = $user->getToken();
            $api = new SpotifyWebAPI();
            $api->setAccessToken($token);

            $client = new Client();
            $res = $client->get('https://api.spotify.com/v1/me/playlists', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' =>  'Bearer ' . $token
                ]
            ]);                   

            $data = json_decode($res->getBody()->getContents())
            $message = '';
    
            foreach($data->items as $item) {
                $message = '* '.$item->name .' '. $item->href;
            }

            $struct = [
                "blocks" =>
                [
                    [
                        "type" => "section", "text" => ["type" => "mrkdwn", "text" => $message]
                    ]
                ]
            ];
            return (new JsonResponse($struct)); 
        }
    }

}
