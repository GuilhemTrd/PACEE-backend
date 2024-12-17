<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class ApiAuthController extends AbstractController
{

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['message' => 'Missing credentials'], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }
        if (!$user->isStatus()) {
            return $this->json(['message' => 'Account is inactive.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'message' => 'Authentication successful',
            'token' => 'fake_jwt_token',
        ], Response::HTTP_OK);
    }


    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $firstname = $data['firstname'] ?? null;
        $lastname = $data['lastname'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$firstname || !$lastname || !$email || !$password) {
            return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json(['message' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
        }

        $uppercase = preg_match('@[A-Z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $length = strlen($password) >= 8;

        if (!$uppercase || !$number || !$length) {
            return $this->json([
                'message' => 'Password must be at least 8 characters long, include at least one uppercase letter and one number'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($userRepository->findOneBy(['email' => $email])) {
            return $this->json(['message' => 'User already exists'], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setUsername($firstname . ' ' . $lastname);
        $user->setEmail($email);
        $user->setFullName($firstname . ' ' . $lastname);
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setStatus(true);

        $user->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }

}

