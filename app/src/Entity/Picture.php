<?php
namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use App\Enum\Category;


#[ORM\Entity(repositoryClass: PictureRepository::class)]
#[ORM\Table(name: 'pictures',
    indexes: [
        new ORM\Index(name: 'idx_category', columns: ['category'])
    ])]
class Picture {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id", type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, length: 50, nullable: false)]
    private string $name = "";

    #[ORM\Column(name: "image", type: Types::TEXT, nullable: false)]
    private string $image = "";

    #[ORM\Column(name: "description", type: Types::STRING, length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: "category", type: Types::STRING, enumType: Category::class, nullable: false)]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pictures')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    public function __construct(string $name, string $image, string $description, Category $category, ?User $user) {
        $this->name = $name;
        $this->image = $image;
        $this->description =$description;
        $this->category = $category;
        $this->user = $user;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getImage(): string {
        return $this->image;
    }

    public function setImage(string $image): self {
        $this->image = $image;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): Category {
        return $this->category;
    }

    public function setCategory(Category $category): self {
        $this->category = $category;
        return $this;
    }

    public function getUser(): ?User 
    {
        return $this->user;
    }

    public function setUser(?User $user): self 
    {
        $this->user = $user;
        return $this;
    }
}