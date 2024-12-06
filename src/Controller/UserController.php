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

}
