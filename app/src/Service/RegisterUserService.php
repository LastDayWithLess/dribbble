<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterUserService {
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function register(string $name, string $email, string $password): User {
        $this->validateName($name);
        $this->validateEmail($email);   
        $this->validatePassword($password);
        $this->existsUser($email);

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);

        $hashPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashPassword);

        $this->validateEntity($user);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to save picture to database: ' . $e->getMessage());
        }
    }

    private function validateName(string $name): void {
        if (strlen($name) <= 1 || strlen($name) > 50) {
            throw new \InvalidArgumentException('Имя должно содержать больше 0 и меньше 50 символов');
        }
    }

    private function validateEmail(string $email): void {
        if (strlen($email) > 254) {
            throw new \InvalidArgumentException('Email слишком большой'); 
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Невалидный формат email');
        }
    }

    private function validatePassword(string $password): void {
        if (strlen($password) < 6 || strlen($password) > 255) {
            throw new \InvalidArgumentException('Пароль не может быть меньше 6 и больше 255');
        }
    }

    private function existsUser(string $email): void {
        $result = $this->userRepository->existsUser($email);

        if ($result) {
            throw new \RuntimeException("Пользователь уже существует");
        }
    }

    private function validateEntity(User $user): void {
        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }
    }
}   