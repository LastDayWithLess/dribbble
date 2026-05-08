<?php
namespace App\Service;

use App\Enum\Category;
use App\Entity\Picture;
use App\Entity\User;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService {
    private const UPLOAD_DIR = 'img';
    private const FILE_PERMISSIONS = 0777;

    public function __construct(
        private PictureRepository $pictureRepository,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private int $pictureMaxSize,
        private array $pictureAllowedMimes,
        private array $pictureAllowedExtensions
    ) {}

    public function getPictures(int $page, ?string $categoryString = null): array {
        $countPicture = 0;

        if ($categoryString) {
            $this->validatePictureCategory($categoryString);
            $category = Category::from($categoryString);

            $countPicture = $this->pictureRepository->countPicture($category);
        } else {
            $countPicture = $this->pictureRepository->countAllPicture();
        }

            $limit = $this->pictureRepository::LIMIT; 
            $maxPage = ceil($countPicture / $limit);

            if ($page <= 0 || $page > $maxPage) {
                throw new \InvalidArgumentException('Страницы не существует');
            }

            $offset = ($page-1) * $limit;

            if ($categoryString) {
                $result = $this->pictureRepository->selectPicture($offset, $category);
            } else {
                $result = $this->pictureRepository->selectAllPicture($offset);
            }

            return $result;
    }

    public function createPicture(string $name, UploadedFile $image, string $description, string $categoryString, ?User $user): string {
        $this->checkSizeImage($image);
        $this->checkMIMEType($image);
        $extension = $this->checkExtension($image);
        $this->validatePictureCategory($categoryString);
        $category = Category::from($categoryString);

        $filePath = $this->saveImageFile($image, $name, $extension, $user);
        $picture = new Picture($name, $filePath, $description, $category, $user);

        try {
            $errors = $this->validator->validate($picture);
            if (count($errors) > 0) {
                throw new \Exception((string) $errors);
            }

            $this->entityManager->persist($picture);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->cleanupFileOnError($picture->getImage());
            throw new \RuntimeException('Ошибка сохранения изображения в базу данных: ' . $e->getMessage());
        }

        return $filePath;
    }

    private function checkSizeImage(UploadedFile $imageFile): void {  
        if (!$imageFile->isValid()) {
            throw new \RuntimeException('Ошибка загрузки файла: ' . $imageFile->getErrorMessage());
        }
        
        if ($imageFile->getSize() > $this->pictureMaxSize) {
            $maxMb = $this->pictureMaxSize / 1024 / 1024;
            throw new \InvalidArgumentException(sprintf('Файл слишком большой. Максимальный размер: %d MB', $maxMb));
        }
    }

    private function checkMIMEType(UploadedFile $imageFile): void {
        if (!in_array($imageFile->getMimeType(), $this->pictureAllowedMimes)) {
            throw new \InvalidArgumentException(sprintf(
                'Неправильный тип файла. Доступные типы: %s', 
                implode(', ', $this->pictureAllowedMimes)
            ));
        }
    }

    private function checkExtension(UploadedFile $imageFile): string {
        $extension = $imageFile->getClientOriginalExtension();
        
        if (!in_array($extension, $this->pictureAllowedExtensions)) {
            throw new \InvalidArgumentException(sprintf(
                'Неправильный тип файла. Доступные типы: %s', 
                implode(', ', $this->pictureAllowedExtensions)
            ));
        }

        return $extension;
    }

    private function validatePictureCategory(string $categoryString): void 
    {
        $allowedValues = array_column(Category::cases(), 'value');
        
        if (!in_array($categoryString, $allowedValues, true)) {
            $allowedList = implode(', ', $allowedValues);
            throw new \InvalidArgumentException(
                sprintf('Недопустимая категория "%s". Разрешенные категории: %s', $categoryString, $allowedList)
            );
        }
    }

    private function saveImageFile(UploadedFile $image, string $name, string $extension, User $user): string { 
        $uploadDir = __DIR__ . '/../../public/' . self::UPLOAD_DIR . '/' . $user->getName();
    
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, self::FILE_PERMISSIONS, true);
        }

        $fileName = $this->generateUniqueFileName($name) . '.' . $extension;
        $filePath = self::UPLOAD_DIR . '/' . $user->getName() . '/' . $fileName;

        $image->move($uploadDir, $fileName);

        return $filePath;
    }

    private function generateUniqueFileName(string $originalName): string {
        $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
        return uniqid() . '_' . $cleanName;
    }

    private function cleanupFileOnError(string $filePath): void
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}