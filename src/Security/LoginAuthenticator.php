<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;

class LoginAuthenticator extends AbstractAuthenticator
{
    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && '/login' === $request->getPathInfo();
    }

    public function authenticate(Request $request): Passport
    {
        $password = $request->request->get('_password');
        $username = $request->request->get('_username');
        $csrfToken = $request->request->get('_csrf_token');
        // ... validate no parameter is empty
        $passport= new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [new CsrfTokenBadge('login', $csrfToken)]
        );
        return $passport;
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        // read the attribute value
        return new CustomOauthToken($passport->getUser(), $passport->getAttribute('scope'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}