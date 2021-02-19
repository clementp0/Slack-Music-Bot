<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index($request)
    {
        $data = ["falcon", "sky", "cloud", "orange", "wood", "forest"];

        header('Content-type:application/json;charset=utf-8');
        echo json_encode($data[0]);
    }
}
