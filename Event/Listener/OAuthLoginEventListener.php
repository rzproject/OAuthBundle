<?php



namespace Rz\OAuthBundle\Event\Listener;


use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Router;

use Sonata\PageBundle\Request\SiteRequest as Request;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Rz\UserBundle\Model\PasswordExpireConfigManagerInterface;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class OAuthLoginEventListener
{
    protected $sessionChecker;
    protected $sessionTokenStorage;
    protected $sessionAuthUtils;
    protected $isHWIConnect;
    protected $session;
    protected $router;
    protected $forceCompleteRegistration;

    public function __construct(ChainRouter $router, Session $session, $sessionChecker, $sessionTokenStorage, $sessionAuthUtils, $isHWIConnect =false, $forceCompleteRegistration=true)
    {
        $this->sessionChecker = $sessionChecker;
        $this->sessionAuthUtils = $sessionAuthUtils;
        $this->sessionTokenStorage = $sessionTokenStorage;
        $this->isHWIConnect = $isHWIConnect;
        $this->session = $session;
        $this->router = $router;
        $this->forceCompleteRegistration = $forceCompleteRegistration;
    }

    public function onOAuthLogin(GetResponseEvent $event)
    {
        // make sure to act on the master request only
        if (!$event->isMasterRequest()) {
			return;
		}
		
        if ($this->sessionTokenStorage->getToken() &&
            !$hasUser = $this->sessionChecker->isGranted('IS_AUTHENTICATED_REMEMBERED') &&
            $this->isHWIConnect) {
            $request =  $event->getRequest();
            $error = $this->sessionAuthUtils->getLastAuthenticationError(false);
            if ($error instanceof AccountNotLinkedException &&
                $this->forceCompleteRegistration &&
                $request->get('_route') != 'rz_oauth_registration_complete_registration') {
                $key = time();
                $session = $request->getSession();
                $session->set('_hwi_oauth.registration_error.'.$key, $error);
                $uri = $this->router->generate('rz_oauth_registration_complete_registration', array('key' => $key));
                $event->setResponse(new RedirectResponse($uri));
                $this->sessionAuthUtils->getLastAuthenticationError(true);
            }
        }
    }
}
