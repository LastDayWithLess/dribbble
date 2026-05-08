<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users', uniqueConstraints:
[new ORM\UniqueConstraint(name: 'unique_email', columns: ['email'])])]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: "name", type: Types::STRING, length: 50, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(name: 'email', nullable: false, type: Types::STRING, length: 254)]
    private ?string $email = null;

    #[ORM\Column(name: 'roles', type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(name: 'password', type: Types::STRING, length: 255, nullable: false)]
    private ?string $password = null; 

    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'user', cascade: ['remove'], orphanRemoval: true)]
    private Collection $pictures;

    public function __construct(string $email = null, array $roles = ['ROLE_USER'], string $password = null) {
        $this->email = $email;
        $this->roles = $roles;
        $this->password = $password;
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    public function getUserIdentifier(): string {
        return (string) $this->email;
    }

    public function getPictures(): Collection 
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self 
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setUser($this);
        }
        return $this;
    }

    public function removePicture(Picture $picture): self 
    {
        if ($this->pictures->removeElement($picture)) {
            if ($picture->getUser() === $this) {
                $picture->setUser(null);
            }
        }
        return $this;
    }
}