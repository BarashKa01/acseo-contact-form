<?php

namespace App\Entity;

use App\Repository\UserRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRequestRepository::class)
 */
class UserRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $usermail;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $usermessage;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsermail(): ?string
    {
        return $this->usermail;
    }

    public function setUsermail(string $usermail): self
    {
        $this->usermail = $usermail;

        return $this;
    }

    public function getUsermessage(): ?string
    {
        return $this->usermessage;
    }

    public function setUsermessage(string $usermessage): self
    {
        $this->usermessage = $usermessage;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setStatusFromUpdate()
    {
        $this->status = !$this->status;
    }

}
