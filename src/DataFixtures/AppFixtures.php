<?php

namespace App\DataFixtures;

use App\Entity\Article;
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
        // Génération des utilisateurs
        $users = $this->createUsers($manager);

        // Génération des badges
        $badges = $this->createBadges($manager);

        // Attribution des badges
        $this->assignBadgesToUsers($manager, $users, $badges);

        // Génération des articles
        $this->createArticles($manager);

        // Génération des discussions, commentaires et likes
        $this->createDiscussionsAndInteractions($manager, $users);

        // Sauvegarde en base de données
        $manager->flush();
    }

    private function createUsers(ObjectManager $manager): array
    {
        $users = [];

        // Création de l'utilisateur admin
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setEmail('admin@admin.com');
        $adminUser->setFullName('Administrator');
        $adminUser->setPassword($this->passwordHasher->hashPassword($adminUser, 'Admin123'));
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setCreatedAt(new \DateTimeImmutable());
        $adminUser->setUpdatedAt(new \DateTime());
        $adminUser->setPalmares("Admin Palmares");
        $adminUser->setTime5k(new \DateTime('00:20:00'));
        $adminUser->setTime10k(new \DateTime('00:40:00'));
        $adminUser->setTimeSemi(new \DateTime('01:30:00'));
        $adminUser->setTimeMarathon(new \DateTime('03:30:00'));
        $adminUser->setStatus(true);

        $manager->persist($adminUser);
        $users[] = $adminUser;

        // Création d'utilisateurs normaux
        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setUsername("user$i");
            $user->setEmail("user$i@example.com");
            $user->setFullName("User $i");
            $user->setPassword($this->passwordHasher->hashPassword($user, "User{$i}Pass1"));
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTime());
            $user->setPalmares("Palmares User $i");
            $user->setTime5k(new \DateTime('00:25:00'));
            $user->setTime10k(new \DateTime('00:50:00'));
            $user->setTimeSemi(new \DateTime('01:45:00'));
            $user->setTimeMarathon(new \DateTime('04:00:00'));
            $user->setStatus(true);

            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function createBadges(ObjectManager $manager): array
    {
        $badges = [];

        $badge1 = new Badge();
        $badge1->setName('Finisher');
        $badge1->setDescription('Badge pour avoir terminé une course.');
        $badge1->setSvg('<svg></svg>');
        $badge1->setCreatedAt(new \DateTimeImmutable());
        $badge1->setStatus(true);
        $manager->persist($badge1);
        $badges[] = $badge1;

        $badge2 = new Badge();
        $badge2->setName('Champion');
        $badge2->setDescription('Badge pour avoir gagné une course.');
        $badge2->setSvg('<svg></svg>');
        $badge2->setCreatedAt(new \DateTimeImmutable());
        $badge2->setStatus(true);
        $manager->persist($badge2);
        $badges[] = $badge2;

        return $badges;
    }

    private function assignBadgesToUsers(ObjectManager $manager, array $users, array $badges): void
    {
        foreach ($users as $index => $user) {
            $userBadge = new UserBadge();
            $userBadge->setUser($user);
            $badge = ($index % 2 === 0) ? $badges[0] : $badges[1];
            $userBadge->setBadge($badge);
            $userBadge->setAwardedAt(new \DateTimeImmutable());
            $userBadge->setStatus(true);

            $manager->persist($userBadge);
        }
    }

    private function createArticles(ObjectManager $manager): void
    {
        $titles = [
            'Les meilleures vestes Gore-Tex pour le trail',
            'Comparatif des lampes frontales pour le trail nocturne',
            'Les meilleures chaussures de trail pour les terrains techniques',
            'Les bâtons de trail : essentiels ou superflus ?',
            'L\'importance de la nutrition en trail',
            'Les meilleurs sacs à dos pour le trail',
            'Comment choisir son entraînement de trail ?',
            'Les blessures courantes en trail et comment les prévenir',
            'Les montées courtes vs les montées longues en trail',
            'L\'équipement minimaliste pour le trail',
        ];

        $descriptions = [
            'Découvrez notre sélection des vestes Gore-Tex idéales pour affronter toutes les conditions météorologiques.',
            'Découvrez notre analyse des meilleures lampes frontales pour vos courses de nuit.',
            'Découvrez quelles chaussures de trail offrent le meilleur grip et confort pour les terrains les plus difficiles.',
            'Les bâtons de trail peuvent être un atout, mais sont-ils vraiment nécessaires pour tous les coureurs de trail ?',
            'Apprenez comment adapter votre alimentation pour optimiser vos performances en trail.',
        ];

        $numArticles = random_int(30, 40); // Générer entre 30 et 40 articles

        foreach (range(1, $numArticles) as $index) {
            $title = $titles[array_rand($titles)];
            $description = $descriptions[array_rand($descriptions)];

            $article = new Article();
            $article->setTitle("$title $index"); // Ajouter un numéro pour différencier les articles
            $article->setDescription("Description : $description (Article $index)");
            $article->setContent("<p>Contenu généré automatiquement pour l'article : $title (Article $index)</p>");
            $article->setCreatedAt(new \DateTimeImmutable(sprintf('2024-12-%02d 14:00:00', ($index % 30) + 1)));
            $article->setUpdatedAt(new \DateTime(sprintf('2024-12-%02d 14:30:00', ($index % 30) + 1)));
            $article->setStatus(true);

            $manager->persist($article);
        }
    }


    /**
     * @throws RandomException
     */
    private function createDiscussionsAndInteractions(ObjectManager $manager, array $users): void
    {
        $discussions = [];
        $numDiscussions = random_int(30, 50); // Générer entre 30 et 50 discussions

        foreach (range(1, $numDiscussions) as $i) {
            $discussion = new Discussion();
            $discussion->setUser($users[array_rand($users)]);
            $discussion->setContent("Ceci est le contenu de la discussion $i.");
            $discussion->setCreatedAt(new \DateTimeImmutable());
            $discussion->setUpdatedAt(new \DateTime());
            $discussion->setStatus(true);

            $manager->persist($discussion);
            $discussions[] = $discussion;
        }

        foreach ($discussions as $discussion) {
            $numComments = random_int(10, 20);
            foreach (range(1, $numComments) as $j) {
                $comment = new DiscussionComment();
                $comment->setUser($users[array_rand($users)]);
                $comment->setDiscussion($discussion);
                $comment->setContent("Ceci est un commentaire sur la discussion : {$discussion->getContent()}");
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setUpdatedAt(new \DateTime());
                $comment->setStatus(true);

                $manager->persist($comment);
            }

            $numLikes = random_int(20, 50);
            foreach (range(1, $numLikes) as $k) {
                $like = new DiscussionLike();
                $like->setUser($users[array_rand($users)]);
                $like->setDiscussion($discussion);
                $like->setCreatedAt(new \DateTimeImmutable());
                $like->setStatus(true);

                $manager->persist($like);
            }
        }

    }
}
