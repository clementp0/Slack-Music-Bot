<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Request $request)
    {
<<<<<<< HEAD
        $message = "Hey <@".$request->request->get('user_id').">, how's it going ?";
        $struct = [
            "blocks" => 
            [
                [,
                    "type" => "section", "text" => ["type" => "mrkdwn", "text" => $message]]]];
        return (
            new JsonResponse(json_encode($request->request))    
    );
=======
        $message = "Hey <@".$request->request->get('user_id').">, thanks for submitting your report!";
           $struct = [
        "blocks" => [[ "type" => "section", "text" => ["type" => "mrkdwn", "text" => $message]]]
];
        return new JsonResponse($struct);
>>>>>>> c226d3e9e5f242bd403db7e7f0aa0e9b5668f6d1
    }
}

