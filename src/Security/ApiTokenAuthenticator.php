<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Security\User;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    /**
     * Authenticate the user based on the X-AUTH-TOKEN header
     */
    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-AUTH-TOKEN');

        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // Load user by API key
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['apiKey' => $apiKey]);

        if ($user === null) {
            throw new CustomUserMessageAuthenticationException('Invalid API token');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiKey, fn() => $user)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = ['message' => $exception->getMessageKey()];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }
}
