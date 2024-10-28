<?php
namespace App\Serializer;

use ApiPlatform\State\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserContextBuilder implements SerializerContextBuilderInterface
{
    private $decorated;
    private $security;

    public function __construct(SerializerContextBuilderInterface $decorated, Security $security)
    {
        $this->decorated = $decorated;
        $this->security = $security;
    }

    public function createFromRequest(Request $request, bool $normalization, array|null $extractedAttributes = []): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        // Ajoutez l'utilisateur actuel au contexte s'il est connecté
        if ($user = $this->security->getUser()) {
            $context[AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS]['user'] = $user;
        }

        return $context;
    }
}
