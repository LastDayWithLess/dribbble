<?php
namespace App\Controller; 

use App\Enum\Category;
use App\Service\PictureService;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PictureController extends AbstractController 
{
    public function __construct(private PictureService $pictureServ, private AuthService $authService) {} 

    #[Route('/picture', name: 'picture_create', methods: ['POST'])]
    public function createPicture(Request $request): Response {
        $authUser = $request->headers->get('Authorization');

        $authResult = $this->authService->authenticate($authUser);

        if (!$authResult['user']) {
            return $this->json(['error' => $authResult['error']], $authResult['status']);
        }

        $name = $request->request->get('name');
        $image = $request->files->get('image');
        $description = $request->request->get('description');
        $categoryString = $request->request->get('category');

        if (!$name) {
            return $this->json(['error' => 'Поле "name" обязательно'], Response::HTTP_BAD_REQUEST);
        }   
    
        if (!$image || !$image->isValid()) {
            return $this->json(['error' => 'Файл слишком велик или повреждён'], Response::HTTP_BAD_REQUEST);
        }
        
        if (!$categoryString) {
            return $this->json(['error' => 'Поле "category" обязательно'], Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $result = $this->pictureServ->createPicture($name, $image, $description, $categoryString, $authResult['user']);
            return $this->json(['answ' => $result], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('picture/{offset}', name: 'picture_get', methods: ['GET'])]
    public function getPicture(Request $request, int $offset): Response {
        try{
            $category = $request->query->get('category');

            $result = $this->pictureServ->getPictures($offset, $category);

            $data = [];
            foreach ($result as $picture) {
                $data[] = [
                    'name' => $picture->getName(),
                    'description' => $picture->getDescription(),
                    'image' => $picture->getImage(),
                    'category' => $picture->getCategory(),
                    'user' => $picture->getUser() ? [
                        'id' => $picture->getUser()->getId(),
                        'name' => $picture->getUser()->getName(),
                    ] : null,
                ];
            }

            return $this->json([
                'success' => true,
                'data' => $data,
            ], Response::HTTP_OK);

        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) { 
            return $this->json(['error' => 'Внутренняя ошибка: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}