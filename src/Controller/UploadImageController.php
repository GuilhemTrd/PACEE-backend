<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class UploadImageController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->logger->info('Route called for image upload.');

        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile) {
            $this->logger->error('No file uploaded');
            return new JsonResponse(['error' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }

        $destination = __DIR__ . '/../../public/uploads/article/';
        $filename = uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move($destination, $filename);
            $this->logger->info('File uploaded successfully', ['filename' => $filename]);
            return new JsonResponse(['url' => '/uploads/article/' . $filename], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('File upload failed', ['error' => $e->getMessage()]);
            return new JsonResponse(['error' => 'File upload failed'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
