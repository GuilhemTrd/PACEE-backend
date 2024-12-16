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
     */
    public function testRegisterSuccess(): void
    {
        // Créer le client HTTP
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

        // Effectuer une requête POST
        $client->jsonRequest('POST', '/register', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'Password123',
        ]);

        // Vérifier que le code de réponse est 201 (Created)
        $this->assertResponseStatusCodeSame(201);

        // Vérifier le contenu JSON de la réponse
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);

        $responseData = json_decode($responseContent, true);
        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User registered successfully', $responseData['message']);
    }
}
