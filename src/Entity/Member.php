<?php

namespace App\Entity;

use App\Repository\MemberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 30)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $lastName;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $telephone1;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $telephone2;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $avatar;

    #[ORM\Column(type: 'text', nullable: true)]
    private $address;

    #[ORM\Column(type: 'boolean')]
    private $isTreasurer;

    #[ORM\Column(type: 'boolean')]
    private $isPresident;

    #[ORM\ManyToOne(targetEntity: Section::class)]
    private $section;

    #[ORM\Column(type: 'datetime')]
    private $created;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated;

    #[ORM\ManyToOne(targetEntity: Admin::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: Admin::class)]
    private $updatedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getTelephone1(): ?string
    {
        return $this->telephone1;
    }

    public function setTelephone1(?string $telephone1): self
    {
        $this->telephone1 = $telephone1;

        return $this;
    }

    public function getTelephone2(): ?string
    {
        return $this->telephone2;
    }

    public function setTelephone2(?string $telephone2): self
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getIsTreasurer(): ?bool
    {
        return $this->isTreasurer;
    }

    public function setIsTreasurer(bool $isTreasurer): self
    {
        $this->isTreasurer = $isTreasurer;

        return $this;
    }

    public function getIsPresident(): ?bool
    {
        return $this->isPresident;
    }

    public function setIsPresident(bool $isPresident): self
    {
        $this->isPresident = $isPresident;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getCreatedBy(): ?Admin
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Admin $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?Admin
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?Admin $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
