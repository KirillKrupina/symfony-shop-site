<?php

namespace App\Security\Main;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFromAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'main_login';

    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;
    private FlashBagInterface $flashBag;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository,  FlashBagInterface $flashBag)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->flashBag = $flashBag;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        if (!$this->isUserVerified($request)) {
            $this->flashBag->add('warning', 'Failed to login. Email is not verified.');
            throw new AuthenticationException();
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge()
            ]
        );
    }

    public function isUserVerified(Request $request) {
        $credentials = $this->getCredentials($request);
        $user = $this->getUser($credentials);

        return $user->isVerified();
    }

    public function getCredentials(Request $request)
    {
        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider = null)
    {
        return $this->userRepository->findOneBy(['email' => $credentials['email']]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

         return new RedirectResponse($this->urlGenerator->generate('main_profile_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
