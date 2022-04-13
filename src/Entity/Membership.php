<?php

namespace App\Entity;

use App\Repository\MembershipRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembershipRepository::class)]
class Membership
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Section::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $section;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $member;

    #[ORM\Column(type: 'float')]
    private $amount;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comment;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $treasurer;

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

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTreasurer(): ?Member
    {
        return $this->treasurer;
    }

    public function setTreasurer(?Member $treasurer): self
    {
        $this->treasurer = $treasurer;

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
