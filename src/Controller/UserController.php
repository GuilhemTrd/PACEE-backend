<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController
{
    private EntityManagerInterface $entityManager;
    private string $uploadsDirectory;

    public function __construct(EntityManagerInterface $entityManager, string $uploadsDirectory)
    {
        $this->entityManager = $entityManager;
        $this->uploadsDirectory = $uploadsDirectory;
    }
    #[Route('/api/users/{id}/upload-avatar', name: 'upload_avatar', methods: ['POST'])]
    public function uploadAvatar(Request $request, User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $file = $request->files->get('image_profile');
            if (!$file) {
                error_log('Fichier non trouvé dans la requête');
                return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
            }

            // Chemin du répertoire des images de profil
            $imageProfileDirectory = $this->uploadsDirectory . DIRECTORY_SEPARATOR . 'imageProfile';

            // Créer le répertoire s'il n'existe pas
            if (!is_dir($imageProfileDirectory)) {
                mkdir($imageProfileDirectory, 0755, true);
            }

            // Supprimer l'ancienne image si elle existe
            $currentImage = $user->getImageProfile();
            if ($currentImage) {
                $oldImagePath = $this->uploadsDirectory . DIRECTORY_SEPARATOR . ltrim($currentImage, '/');
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                    error_log('Ancienne image supprimée : ' . $oldImagePath);
                } else {
                    error_log('Ancienne image non trouvée pour suppression : ' . $oldImagePath);
                }
            }

            error_log('Type MIME détecté : ' . $file->getMimeType());
            error_log('Extension devinée : ' . $file->guessExtension());

            // Générer un nouveau nom basé sur le username et l'id
            $safeUsername = preg_replace('/[^a-zA-Z0-9-_]/', '', $user->getUsername());
            $filename = $safeUsername . '_' . $user->getId() . '.' . $file->guessExtension();

            // Enregistrer la nouvelle image
            $file->move($imageProfileDirectory, $filename);

            $user->setImageProfile('/uploads/imageProfile/' . $filename);
            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['image_profile' => $user->getImageProfile()]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Une erreur est survenue : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (isset($data['username'])) {
                $user->setUsername($data['username']);
            }
            if (isset($data['email'])) {
                $user->setEmail($data['email']);
            }
            if (isset($data['palmares'])) {
                $user->setPalmares($data['palmares']);
            }
            if (isset($data['time_5k'])) {
                $user->setTime5k(new \DateTime($data['time_5k']));
            }
            if (isset($data['time_10k'])) {
                $user->setTime10k(new \DateTime($data['time_10k']));
            }
            if (isset($data['time_semi'])) {
                $user->setTimeSemi(new \DateTime($data['time_semi']));
            }
            if (isset($data['time_marathon'])) {
                $user->setTimeMarathon(new \DateTime($data['time_marathon']));
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Profil mis à jour avec succès'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la mise à jour du profil : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
