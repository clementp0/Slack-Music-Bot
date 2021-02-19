<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserController extends AbstractController
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index()
    {

        $response = $this->client->request(
            'GET',
            'https://slack.com/api/METHOD_FAMILY.method'
        );

        dd($response);

        return new JsonResponse('test');
    }
}
