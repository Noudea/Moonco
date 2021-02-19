<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity=Server::class, inversedBy="users")
     */
    private $servers;

    /**
     * @ORM\ManyToMany(targetEntity=Server::class, inversedBy="admins")
     * @ORM\JoinTable(name="user_server_admin",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="server_id", referencedColumnName="id")}
     *      )
     * 
     * 
     */
    private $adminServers;

    /**
     * @ORM\ManyToMany(targetEntity=Server::class, inversedBy="moderators")
     * @ORM\JoinTable(name="user_server_moderator",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="server_id", referencedColumnName="id")}
     *      )
     */
    private $moderatorsServers;

    public function __construct()
    {
        $this->servers = new ArrayCollection();
        $this->adminServers = new ArrayCollection();
        $this->moderatorsServers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Server[]
     */
    public function getServers(): Collection
    {
        return $this->servers;
    }

    public function addServer(Server $server): self
    {
        if (!$this->servers->contains($server)) {
            $this->servers[] = $server;
        }

        return $this;
    }

    public function removeServer(Server $server): self
    {
        $this->servers->removeElement($server);

        return $this;
    }

    /**
     * @return Collection|Server[]
     */
    public function getAdminServers(): Collection
    {
        return $this->adminServers;
    }

    public function addAdminServer(Server $adminServer): self
    {
        if (!$this->adminServers->contains($adminServer)) {
            $this->adminServers[] = $adminServer;
            $adminServer->addAdmin($this);
        }

        return $this;
    }

    public function removeAdminServer(Server $adminServer): self
    {
        if ($this->adminServers->removeElement($adminServer)) {
            $adminServer->removeAdmin($this);
        }

        return $this;
    }

    /**
     * @return Collection|Server[]
     */
    public function getModeratorsServers(): Collection
    {
        return $this->moderatorsServers;
    }

    public function addModeratorsServer(Server $moderatorsServer): self
    {
        if (!$this->moderatorsServers->contains($moderatorsServer)) {
            $this->moderatorsServers[] = $moderatorsServer;
            $moderatorsServer->addModerator($this);
        }

        return $this;
    }

    public function removeModeratorsServer(Server $moderatorsServer): self
    {
        if ($this->moderatorsServers->removeElement($moderatorsServer)) {
            $moderatorsServer->removeModerator($this);
        }

        return $this;
    }
}
