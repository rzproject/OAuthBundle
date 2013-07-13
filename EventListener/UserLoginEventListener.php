<?php

/*
 * This file is part of the RzOAuthBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\OAuthBundle\EventListener;

use Rz\UserBundle\Event\UserLoginEvent;
use Rz\UserBundle\RzUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserLoginEventListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            RzUserEvents::RZ_LOGIN_PROCESS => 'processLogin',
        );
    }

    public function processLogin(UserLoginEvent $event)
    {
        $request =  $event->getRequest();
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $connect = $this->container->getParameter('hwi_oauth.connect');
        // get the error if any (works with forward and redirect -- see below)
        $error = $event->getError();

        if ($connect
            && !$hasUser
            && $error instanceof AccountNotLinkedException
        ) {
            $key = time();
            $session = $request->getSession();
            $session->set('_hwi_oauth.registration_error.'.$key, $error);
            $uri = $this->container->get('router')->generate('rz_o_auth_registration_complete_registration', array('key' => $key));
            $event->setResponse(new RedirectResponse($uri));
        }
    }
}
