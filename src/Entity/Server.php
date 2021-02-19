<?php

namespace App\Entity;

use App\Repository\ServerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServerRepository::class)
 */
class Server
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="array")
     */
    private $slug = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * @ORM\OneToMany(targetEntity=Movie::class, mappedBy="relation", orphanRemoval=true)
     */
    private $movies;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="servers")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="adminServers")
     */
    private $admins;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="moderatorsServers")
     */
    private $moderators;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->admins = new ArrayCollection();
        $this->moderators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?array
    {
        return $this->slug;
    }

    public function setSlug(array $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlugInput(): string
    {
        //transformer tableau en string
        return implode(' ', $this->slug);
    }

    public function setSlugInput(?string $slugInput): self
    {
        //transformer le string en tableau dans slug
        if($slugInput) {
            $this->slug = explode(' ', $slugInput);
        }
        return $this;
    }

    public function getPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->setRelation($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getRelation() === $this) {
                $movie->setRelation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addServer($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeServer($this);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(User $admin): self
    {
        if (!$this->admins->contains($admin)) {
            $this->admins[] = $admin;
        }

        return $this;
    }

    public function removeAdmin(User $admin): self
    {
        $this->admins->removeElement($admin);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getModerators(): Collection
    {
        return $this->moderators;
    }

    public function addModerator(User $moderator): self
    {
        if (!$this->moderators->contains($moderator)) {
            $this->moderators[] = $moderator;
        }

        return $this;
    }

    public function removeModerator(User $moderator): self
    {
        $this->moderators->removeElement($moderator);

        return $this;
    }
}
