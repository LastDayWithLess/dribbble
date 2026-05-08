<?php
namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthService {
    public function __construct(
        private JWTService $jwtService,
        private EntityManagerInterface $em
    ) {}

    public function authenticate(?string $authUser = null): array {
        if (!$authUser || !str_starts_with($authUser, 'Bearer ')) {
            return [
                'user' => null,
                'error' => 'Требуется аунтефикация. Укажите Bearer токен',
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }

        $token = substr($authUser, 7);

        $payload = $this->jwtService->decodeToken($token);

        if (!$payload) {
            return [
                'user' => null,
                'error' => 'Недействительный токен',
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return [
                'user' => null,
                'error' => 'Токен истек',
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }

        if (!isset($payload['email'])) {
            return [
                'user' => null,
                'error' => 'Недействительный токен. Отсутствует email',
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $payload['email']]);
        if (!$user) {
            return [
                'user' => null,
                'error' => 'Пользователь не найден',
                'status' => Response::HTTP_UNAUTHORIZED
            ];
        }

        $userRoles = $user->getRoles();
        if (!in_array('ROLE_USER', $userRoles)) {
            return [
                'user' => null,
                'error' => 'Доступ запрещен. Требуется роль ROLE_USER',
                'status' => Response::Response::HTTP_FORBIDDEN
            ];
        }

        return [
            'user' => $user,
            'error' => null,
            'status' => null
        ];
    }
}