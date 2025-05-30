<?php

namespace App\User\Domain\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

interface AuthenticatorServiceInterface
{
    public function authenticate(Request $request): Passport;
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response;
}