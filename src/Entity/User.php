<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $full_name = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_profile = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $palmares = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time_5k = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time_10k = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time_semi = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time_marathon = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column]
    private ?bool $status = null;

    /**
     * @var Collection<int, UserBadge>
     */
    #[ORM\OneToMany(targetEntity: UserBadge::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $userBadges;

    /**
     * @var Collection<int, Discussion>
     */
    #[ORM\OneToMany(targetEntity: Discussion::class, mappedBy: 'user')]
    private Collection $discussions;

    /**
     * @var Collection<int, DiscussionComment>
     */
    #[ORM\OneToMany(targetEntity: DiscussionComment::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $discussionComments;

    /**
     * @var Collection<int, DiscussionLike>
     */
    #[ORM\OneToMany(targetEntity: DiscussionLike::class, mappedBy: 'user')]
    private Collection $discussionLikes;

    /**
     * @var Collection<int, Article>
     */
    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'user')]
    private Collection $articles;

    /**
     * @var Collection<int, ArticleLike>
     */
    #[ORM\OneToMany(targetEntity: ArticleLike::class, mappedBy: 'user')]
    private Collection $articleLikes;

    public function __construct()
    {
        $this->userBadges = new ArrayCollection();
        $this->discussions = new ArrayCollection();
        $this->discussionComments = new ArrayCollection();
        $this->discussionLikes = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->articleLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(?string $full_name): static
    {
        $this->full_name = $full_name;

        return $this;
    }
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }


    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getImageProfile(): ?string
    {
        return $this->image_profile;
    }

    public function setImageProfile(?string $image_profile): static
    {
        $this->image_profile = $image_profile;

        return $this;
    }

    public function getPalmares(): ?string
    {
        return $this->palmares;
    }

    public function setPalmares(?string $palmares): static
    {
        $this->palmares = $palmares;

        return $this;
    }

    public function getTime5k(): ?\DateTimeInterface
    {
        return $this->time_5k;
    }

    public function setTime5k(?\DateTimeInterface $time_5k): static
    {
        $this->time_5k = $time_5k;

        return $this;
    }

    public function getTime10k(): ?\DateTimeInterface
    {
        return $this->time_10k;
    }

    public function setTime10k(?\DateTimeInterface $time_10k): static
    {
        $this->time_10k = $time_10k;

        return $this;
    }

    public function getTimeSemi(): ?\DateTimeInterface
    {
        return $this->time_semi;
    }

    public function setTimeSemi(?\DateTimeInterface $time_semi): static
    {
        $this->time_semi = $time_semi;

        return $this;
    }

    public function getTimeMarathon(): ?\DateTimeInterface
    {
        return $this->time_marathon;
    }

    public function setTimeMarathon(?\DateTimeInterface $time_marathon): static
    {
        $this->time_marathon = $time_marathon;

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

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
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
            $userBadge->setUser($this);
        }

        return $this;
    }

    public function removeUserBadge(UserBadge $userBadge): static
    {
        if ($this->userBadges->removeElement($userBadge)) {
            // set the owning side to null (unless already changed)
            if ($userBadge->getUser() === $this) {
                $userBadge->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setUser($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            // set the owning side to null (unless already changed)
            if ($discussion->getUser() === $this) {
                $discussion->setUser(null);
            }
        }

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
            $discussionComment->setUser($this);
        }

        return $this;
    }

    public function removeDiscussionComment(DiscussionComment $discussionComment): static
    {
        if ($this->discussionComments->removeElement($discussionComment)) {
            // set the owning side to null (unless already changed)
            if ($discussionComment->getUser() === $this) {
                $discussionComment->setUser(null);
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
            $discussionLike->setUser($this);
        }

        return $this;
    }

    public function removeDiscussionLike(DiscussionLike $discussionLike): static
    {
        if ($this->discussionLikes->removeElement($discussionLike)) {
            // set the owning side to null (unless already changed)
            if ($discussionLike->getUser() === $this) {
                $discussionLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArticleLike>
     */
    public function getArticleLikes(): Collection
    {
        return $this->articleLikes;
    }

    public function addArticleLike(ArticleLike $articleLike): static
    {
        if (!$this->articleLikes->contains($articleLike)) {
            $this->articleLikes->add($articleLike);
            $articleLike->setUser($this);
        }

        return $this;
    }

    public function removeArticleLike(ArticleLike $articleLike): static
    {
        if ($this->articleLikes->removeElement($articleLike)) {
            // set the owning side to null (unless already changed)
            if ($articleLike->getUser() === $this) {
                $articleLike->setUser(null);
            }
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
