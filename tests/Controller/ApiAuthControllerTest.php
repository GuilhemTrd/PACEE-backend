<?php

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\ApiAuthController
 */
class ApiAuthControllerTest extends WebTestCase
{
    /**
     * @covers \App\Controller\ApiAuthController::register
     * @group authentication
     */
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // Nettoyer la base de données
        $entityManager = $container->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            $connection->executeStatement('TRUNCATE TABLE ' . $metadata->getTableName());
        }
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1;');

        // Requête POST vers /register
        $client->jsonRequest('POST', '/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password123',
        ]);

        $this->assertResponseStatusCodeSame(201);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User registered successfully', $responseData['message']);
    }

    /**
     * @covers \App\Controller\ApiAuthController::login
     * @group authentication
     */
    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        // Nettoyer la base de données
        $entityManager = $container->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            $connection->executeStatement('TRUNCATE TABLE ' . $metadata->getTableName());
        }
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1;');

        // Ajouter un utilisateur valide dans la base de données
        $user = new \App\Entity\User();
        $user->setUsername('John Doe');
        $user->setEmail('john.doe@example.com');
        $user->setFullName('John Doe');
        $user->setRoles(['ROLE_USER']);
        $passwordHasher = $container->get('security.password_hasher');
        $user->setPassword($passwordHasher->hashPassword($user, 'Password123'));
        $user->setStatus(true);
        $user->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($user);
        $entityManager->flush();

        // Requête POST vers /login avec des identifiants valides
        $client->jsonRequest('POST', '/login', [
            'email' => 'john.doe@example.com',
            'password' => 'Password123',
        ]);

        // Vérifications
        $this->assertResponseStatusCodeSame(200);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);

        // Vérifiez que le JWT ou un message de succès est retourné
        $this->assertArrayHasKey('token', $responseData);
        $this->assertNotEmpty($responseData['token']);
    }


    /**
     * @covers \App\Controller\ApiAuthController::login
     * @group authentication
     */
    public function testLoginFailure(): void
    {
        $client = static::createClient();

        // Requête POST vers /login avec des identifiants invalides
        $client->jsonRequest('POST', '/login', [
            'email' => 'invalid_user@example.com',
            'password' => 'invalid_password',
        ]);

        $this->assertResponseStatusCodeSame(401);

        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('Invalid credentials.', $responseData['message']); // Mise à jour ici
    }

}
