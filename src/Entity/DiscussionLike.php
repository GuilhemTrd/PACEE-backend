<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\DiscussionLikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DiscussionLikeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['discussion_like:read']],
    denormalizationContext: ['groups' => ['discussion_like:write']]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'user' => 'exact',
    'discussion' => 'exact'
])]
class DiscussionLike
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['discussion_like:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discussionLikes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['discussion_like:read', 'discussion_like:write'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'discussionLikes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['discussion_like:read', 'discussion_like:write'])]
    private ?Discussion $discussion = null;

    #[ORM\Column]
    #[Groups(['discussion_like:read'])]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    #[Groups(['discussion_like:read', 'discussion_like:write'])]
    private ?bool $status = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->status = true;
    }

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

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): static
    {
        $this->discussion = $discussion;

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
}
