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
        $message = "Hey <@".$request->request->get('admin.teams.list').">, how\'s it going ?";
        $struct = [
            "blocks" => 
            [
                [
                    "type" => "section", "text" => ["type" => "mrkdwn", "text" => $message]]]];
        return (
            new JsonResponse($struct)
            
    );
    }
}

