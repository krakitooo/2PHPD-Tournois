<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessHandler
{
    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();

        if (!isset($data['token'])) {
            return;
        }

        $token = $data['token'];

        $event->getResponse()->headers->setCookie(
            Cookie::create(
                'BEARER',
                $token,
                new \DateTime('+1 hour'),
                '/',
                null,
                true,    // secure
                true,    // httpOnly
                false,
                'strict' // samesite
            )
        );
    }
}
