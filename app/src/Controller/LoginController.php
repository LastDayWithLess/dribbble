<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\LoginUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    public function __construct(private LoginUserService $loginServ) {}

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $body = $request->getContent(); 
        $data = json_decode($body, true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            return $this->json(['error' => 'Поля email и password не могут быть пустыми']);
        }

        try {
            $token = $this->loginServ->generateToken($email, $password);

            return $this->json(['user' => $email, 'token' => $token], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
