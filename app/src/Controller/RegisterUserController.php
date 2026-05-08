<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\RegisterUserService;

class RegisterUserController extends AbstractController {
    public function __construct(private RegisterUserService $regServ) {}

    #[Route('/register', name: 'registration_user', methods: ['POST'])]
    public function registerUser(Request $request): Response {
        $body = $request->getContent();

        $data = json_decode($body, true);
        if ($data === null) {
            return $this->json([
                'error' => 'неправильный формат json'
            ], Response::HTTP_BAD_REQUEST);
        }

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$name) {
            return $this->json(['error' => 'Поле "name" обязательно'], Response::HTTP_BAD_REQUEST);
        }   
    
        if (!$email) {
            return $this->json(['error' => 'Поле "email" обязательно'], Response::HTTP_BAD_REQUEST);
        }
        
        if (!$password) {
            return $this->json(['error' => 'Поле "password" обязательно'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $answ = $this->regServ->register($name, $email, $password);
            return $this->json(['created' => true, 'name' => $answ->getName()], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}