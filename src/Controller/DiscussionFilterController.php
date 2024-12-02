<?php

namespace App\Controller;

use App\Entity\Discussion;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DiscussionFilterController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/custom-filter', name: 'api_custom_filter', methods: ['GET'])]
    public function filterDiscussions(Request $request, LoggerInterface $logger): JsonResponse
    {
        $logger->info('Route atteinte, début traitement');
        $filter = $request->query->get('filter', 'recent');
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 10);

        $offset = ($page - 1) * $itemsPerPage;

        $repository = $this->entityManager->getRepository(Discussion::class);

        if ($filter === 'trending') {
            $now = new \DateTime();
            $lastWeek = (clone $now)->modify('-7 days');

            $query = $repository->createQueryBuilder('d')
                ->addSelect(
                    '(COUNT(DISTINCT c.id) * 2 + COUNT(DISTINCT l.id)) AS HIDDEN trendScore'
                )
                ->leftJoin('d.discussionComments', 'c', 'WITH', 'c.created_at > :lastWeek')
                ->leftJoin('d.discussionLikes', 'l', 'WITH', 'l.created_at > :lastWeek')
                ->groupBy('d.id')
                ->orderBy('trendScore', 'DESC')
                ->addOrderBy('d.created_at', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($itemsPerPage)
                ->setParameter('lastWeek', $lastWeek)
                ->getQuery();
        } elseif ($filter === 'most_liked') {
            $query = $repository->createQueryBuilder('d')
                ->addSelect('COUNT(dl.id) AS HIDDEN likeCount')
                ->leftJoin('d.discussionLikes', 'dl')
                ->groupBy('d.id')
                ->orderBy('likeCount', 'DESC')
                ->addOrderBy('d.created_at', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($itemsPerPage)
                ->getQuery();
        } else {
            $query = $repository->createQueryBuilder('d')
                ->orderBy('d.created_at', 'DESC')
                ->setFirstResult($offset)
                ->setMaxResults($itemsPerPage)
                ->getQuery();
        }

        $discussions = $query->getResult();

        $totalItems = $repository->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $data = [
            '@context' => '/api/contexts/Discussion',
            '@id' => '/api/discussions',
            '@type' => 'Collection',
            'totalItems' => (int) $totalItems,
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
            'member' => array_map(function (Discussion $discussion) use ($filter) {
                $trendScore = 0;
                if ($filter === 'trending') {
                    $trendScore = (count($discussion->getDiscussionComments()) * 2) +
                        (count($discussion->getDiscussionLikes()) * 1);
                }

                return [
                    '@id' => '/api/discussions/' . $discussion->getId(),
                    '@type' => 'Discussion',
                    'id' => $discussion->getId(),
                    'user' => [
                        '@id' => '/api/users/' . $discussion->getUser()->getId(),
                        '@type' => 'User',
                        'username' => $discussion->getUser()->getUsername(),
                    ],
                    'content' => $discussion->getContent(),
                    'created_at' => $discussion->getCreatedAt()->format('Y-m-d\TH:i:sP'),
                    'updated_at' => $discussion->getUpdatedAt()->format('Y-m-d\TH:i:sP'),
                    'status' => $discussion->isStatus(),
                    'discussionComments' => array_map(function ($comment) {
                        return $comment->getId();
                    }, $discussion->getDiscussionComments()->toArray()),
                    'discussionLikes' => array_map(function ($like) {
                        return $like->getId();
                    }, $discussion->getDiscussionLikes()->toArray()),
                    'commentCount' => $discussion->getCommentCount(),
                    'likeCount' => $discussion->getLikeCount(),
                    'trendScore' => $trendScore,
                    'userLiked' => $discussion->hasUserLiked(),
                ];
            }, $discussions),
        ];

        return new JsonResponse($data);
    }
}
