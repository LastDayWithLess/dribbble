<?php
namespace App\Service;

use App\Entity\User;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginUserService {
    public function __construct(
        private EntityManagerInterface $managerEntity,
        private JWTService $jwtService,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function generateToken(string $email, string $password): string {
        $user = $this->managerEntity->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new \Exception('Неверные учетные данные');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new \Exception('Неверные учетные данные');
        }

        $payload = [
            'sub' => $user->getUserIdentifier(),
            'userId' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];

        $token = $this->jwtService->createToken($payload);

        return $token;
    }
}