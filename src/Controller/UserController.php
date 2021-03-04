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
        return new JsonResponse(utf8_decode("%3C@".$request->request->get('user_id')."%3E"));
    }
}
