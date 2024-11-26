<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BadgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ApiResource(normalizationContext: ['groups' => ['badge:read']], denormalizationContext: ['groups' => ['badge:write']])]
class Badge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['badge:read', 'userBadge:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['badge:read', 'userBadge:read', 'user:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['badge:read', 'userBadge:read', 'user:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['badge:read', 'userBadge:read', 'user:read'])]
    private ?string $svg = null;

    #[ORM\Column]
    #[Groups(['badge:read', 'userBadge:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['badge:read', 'userBadge:read'])]
    private ?bool $status = null;

    /**
     * @var Collection<int, UserBadge>
     */
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'badge', orphanRemoval: true)]
    private Collection $userBadges;

    public function __construct()
    {
        $this->userBadges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSvg(): ?string
    {
        return $this->svg;
    }

    public function setSvg(string $svg): static
    {
        $this->svg = $svg;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, UserBadge>
     */
    public function getUserBadges(): Collection
    {
        return $this->userBadges;
    }

    public function addUserBadge(UserBadge $userBadge): static
    {
        if (!$this->userBadges->contains($userBadge)) {
            $this->userBadges->add($userBadge);
            $userBadge->setBadge($this);
        }

        return $this;
    }

    public function removeUserBadge(UserBadge $userBadge): static
    {
        if ($this->userBadges->removeElement($userBadge)) {
            // set the owning side to null (unless already changed)
            if ($userBadge->getBadge() === $this) {
                $userBadge->setBadge(null);
            }
        }

        return $this;
    }
}
