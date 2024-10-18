<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DiscussionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
#[ApiResource]
class Discussion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discussions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column]
    private ?bool $status = null;

    /**
     * @var Collection<int, DiscussionComment>
     */
    #[ORM\OneToMany(targetEntity: DiscussionComment::class, mappedBy: 'discussion')]
    private Collection $discussionComments;

    /**
     * @var Collection<int, DiscussionLike>
     */
    #[ORM\OneToMany(targetEntity: DiscussionLike::class, mappedBy: 'discussion')]
    private Collection $discussionLikes;

    public function __construct()
    {
        $this->discussionComments = new ArrayCollection();
        $this->discussionLikes = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

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
     * @return Collection<int, DiscussionComment>
     */
    public function getDiscussionComments(): Collection
    {
        return $this->discussionComments;
    }

    public function addDiscussionComment(DiscussionComment $discussionComment): static
    {
        if (!$this->discussionComments->contains($discussionComment)) {
            $this->discussionComments->add($discussionComment);
            $discussionComment->setDiscussion($this);
        }

        return $this;
    }

    public function removeDiscussionComment(DiscussionComment $discussionComment): static
    {
        if ($this->discussionComments->removeElement($discussionComment)) {
            // set the owning side to null (unless already changed)
            if ($discussionComment->getDiscussion() === $this) {
                $discussionComment->setDiscussion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DiscussionLike>
     */
    public function getDiscussionLikes(): Collection
    {
        return $this->discussionLikes;
    }

    public function addDiscussionLike(DiscussionLike $discussionLike): static
    {
        if (!$this->discussionLikes->contains($discussionLike)) {
            $this->discussionLikes->add($discussionLike);
            $discussionLike->setDiscussion($this);
        }

        return $this;
    }

    public function removeDiscussionLike(DiscussionLike $discussionLike): static
    {
        if ($this->discussionLikes->removeElement($discussionLike)) {
            // set the owning side to null (unless already changed)
            if ($discussionLike->getDiscussion() === $this) {
                $discussionLike->setDiscussion(null);
            }
        }

        return $this;
    }
}
