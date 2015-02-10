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


class OAuthLoginEventListener
{
    protected $securityContext;
    protected $isHWIConnect;
    protected $session;
    protected $router;

    public function __construct(ChainRouter $router, SecurityContext $securityContext, Session $session, $isHWIConnect =false)
    {
        $this->securityContext = $securityContext;
        $this->isHWIConnect = $isHWIConnect;
        $this->session = $session;
        $this->router = $router;
    }

    public function onOAuthLogin(GetResponseEvent $event)
    {
        // make sure to act on the master request only
        if (!$event->isMasterRequest()) {
			return;
		}
		
        if ($this->securityContext->getToken() &&
            !$hasUser = $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') &&
            $this->isHWIConnect) {
            $request =  $event->getRequest();
            $error = $this->getErrorForRequest($request);
            if ($error instanceof AccountNotLinkedException &&
               $request->get('_route') != 'rz_oauth_registration_complete_registration') {
                $key = time();
                $session = $request->getSession();
                $session->set('_hwi_oauth.registration_error.'.$key, $error);
                $uri = $this->router->generate('rz_oauth_registration_complete_registration', array('key' => $key));
                $event->setResponse(new RedirectResponse($uri));
            }
        }
    }


    /**
     * Get the security error for a given request.
     *
     * @param Request $request
     *
     * @return string|\Exception
     */
    protected function getErrorForRequest($request)
    {
        //just get the error do not remove the error form the session
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        return $error;
    }
}
