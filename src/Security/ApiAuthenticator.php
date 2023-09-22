<?php

namespace App\Security;

use App\Manager\ClientManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    private EntityManagerInterface $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    /**
     * In the case the request concern and API endpoint, it requires Authorisation header
     */
    public function supports(Request $request): ?bool {
        return str_contains($request->getRequestUri(), "/api/");
    }

    public function authenticate(Request $request): Passport {
        $authorizationHeader = $request->headers->get('Authorization');

        if (null === $authorizationHeader) {
            throw new CustomUserMessageAuthenticationException('No credentials provided');
        } else {
            $authorizationHeader = str_replace("Basic ", "", $authorizationHeader);
            $authorizationHeaderDecoded = base64_decode($authorizationHeader);

            $loginPassword = explode(":", $authorizationHeaderDecoded);
            $clientManager = new ClientManager($this->manager);
            $client = $clientManager->getClientByLogin($loginPassword[0]);

            if (!$client || !password_verify($loginPassword[1], $client->getPassword())) {
                throw new CustomUserMessageAuthenticationException('Incorrect password or login');
            }
        }

        return new SelfValidatingPassport(new UserBadge($loginPassword[0]));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}