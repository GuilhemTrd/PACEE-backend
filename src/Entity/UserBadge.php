<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserBadgeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserBadgeRepository::class)]
#[ApiResource]
class UserBadge
{
    #[ORM\ManyToOne(inversedBy: 'badge')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['userBadge:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userBadges')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['userBadge:read', 'user:read'])]
    private ?Badge $badge = null;

    #[ORM\Column]
    #[Groups(['userBadge:read', 'user:read'])]
    private ?\DateTimeImmutable $awarded_at = null;

    #[ORM\Column]
    #[Groups(['userBadge:read', 'user:read'])]
    private ?bool $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function getAwardedAt(): ?\DateTimeImmutable
    {
        return $this->awarded_at;
    }

    public function setAwardedAt(\DateTimeImmutable $awarded_at): static
    {
        $this->awarded_at = $awarded_at;

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
}
