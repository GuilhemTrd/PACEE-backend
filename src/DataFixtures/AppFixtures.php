<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Badge;
use App\Entity\Discussion;
use App\Entity\DiscussionComment;
use App\Entity\DiscussionLike;
use App\Entity\UserBadge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création de quelques badges
        $badge1 = new Badge();
        $badge1->setName('Finisher');
        $badge1->setDescription('Badge pour avoir terminé une course.');
        $badge1->setSvg('<svg></svg>');
        $badge1->setCreatedAt(new \DateTimeImmutable());
        $badge1->setStatus(true);
        $manager->persist($badge1);

        $badge2 = new Badge();
        $badge2->setName('Champion');
        $badge2->setDescription('Badge pour avoir gagné une course.');
        $badge2->setSvg('<svg></svg>');
        $badge2->setCreatedAt(new \DateTimeImmutable());
        $badge2->setStatus(true);
        $manager->persist($badge2);

        // Création d'un utilisateur admin
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setEmail('admin@admin.com');
        $adminUser->setFullName('Administrator');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'Admin123'));
        $adminUser->setRoles(['ROLE_ADMIN']); // Ajout du rôle admin
        $adminUser->setCreatedAt(new \DateTimeImmutable());
        $adminUser->setUpdatedAt(new \DateTime());
        $adminUser->setPalmares("Admin Palmares");
        $adminUser->setTime5k(new \DateTime('00:20:00')); // Exemple de temps pour 5K
        $adminUser->setTime10k(new \DateTime('00:40:00'));
        $adminUser->setTimeSemi(new \DateTime('01:30:00'));
        $adminUser->setTimeMarathon(new \DateTime('03:30:00'));
        $adminUser->setStatus(true);
        $manager->persist($adminUser);

        // Création d'utilisateurs normaux
        $users = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername("user$i");
            $user->setEmail("user$i@example.com");
            $user->setFullName("User $i");
            $password = "User{$i}Pass1";
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTime());
            $user->setPalmares("Palmares User $i");
            $user->setTime5k(new \DateTime('00:25:00')); // Temps générique pour le 5K
            $user->setTime10k(new \DateTime('00:50:00'));
            $user->setTimeSemi(new \DateTime('01:45:00'));
            $user->setTimeMarathon(new \DateTime('04:00:00'));
            $user->setStatus(true);

            $manager->persist($user);
            $users[] = $user;
        }

        // Attribution des badges via UserBadge
        foreach ($users as $index => $user) {
            $userBadge = new UserBadge();
            $userBadge->setUser($user);

            // Associe un badge selon l'index (pair/impair)
            $badge = ($index % 2 === 0) ? $badge1 : $badge2;
            $userBadge->setBadge($badge);
            $userBadge->setAwardedAt(new \DateTimeImmutable());
            $userBadge->setStatus(true);

            $manager->persist($userBadge);
        }

        // Création de quelques discussions
        $discussions = [];
        for ($i = 1; $i <= 5; $i++) {
            $discussion = new Discussion();
            $discussion->setUser($users[array_rand($users)]);
            $discussion->setContent("Ceci est le contenu de la discussion $i");
            $discussion->setCreatedAt(new \DateTimeImmutable());
            $discussion->setUpdatedAt(new \DateTime());
            $discussion->setStatus(true);

            $manager->persist($discussion);
            $discussions[] = $discussion;
        }

        // Création de commentaires de discussions
        for ($i = 1; $i <= 10; $i++) {
            $comment = new DiscussionComment();
            $comment->setUser($users[array_rand($users)]);
            $comment->setDiscussion($discussions[array_rand($discussions)]);
            $comment->setContent("Ceci est le commentaire $i");
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTime());
            $comment->setStatus(true);

            $manager->persist($comment);
        }

        // Création de likes de discussions
        for ($i = 1; $i <= 20; $i++) {
            $like = new DiscussionLike();
            $like->setUser($users[array_rand($users)]);
            $like->setDiscussion($discussions[array_rand($discussions)]);
            $like->setCreatedAt(new \DateTimeImmutable());
            $like->setStatus(true);

            $manager->persist($like);
        }

        // Sauvegarde des entités dans la base de données
        $manager->flush();
    }
}
