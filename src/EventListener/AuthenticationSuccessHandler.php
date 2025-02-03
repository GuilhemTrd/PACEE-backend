<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Méthode déclenchée après une authentification réussie
     *
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $this->logger->info('Authentication success handler triggered.');

        // Récupération des données de l'événement
        $data = $event->getData();
        $user = $event->getUser();

        // Vérification que l'utilisateur est bien une instance de UserInterface
        if (!$user instanceof UserInterface) {
            $this->logger->error('Invalid user instance.');
            return;
        }

        // Vérification de la présence du token dans les données
        if (!isset($data['token'])) {
            $this->logger->error('No token found in response data.');
        } else {
            // Log du token généré
            $this->logger->info('Token generated: ' . $data['token']);
        }

        // Dans l'événement de génération de token
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'full_name' => $user->getFullName(),
            'roles' => $user->getRoles(),
        ];

        // Log des informations utilisateur
        $this->logger->info('User data added: ' . json_encode($data['user']));

        // Mise à jour des données de l'événement
        $event->setData($data);
    }
}
