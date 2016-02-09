<?php



namespace Rz\OAuthBundle\Controller;

use Rz\UserBundle\Controller\RegistrationSonataUserController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use FOS\UserBundle\Model\UserInterface;
use Rz\UserBundle\RzUserEvents;
use Rz\UserBundle\Event\RzUserEvent;

class OAuthRegistrationController extends RegistrationSonataUserController
{

    public function registerAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($user instanceof UserInterface) {
            $this->container->get('session')->getFlashBag()->set('rz_user_error', 'sonata_user_already_authenticated');
            $url = $this->container->get('router')->generate('fos_user_profile_show');

            return new RedirectResponse($url);
        }

        $form = $this->container->get('rz.user.registration.form');
        $formHandler = $this->container->get('rz.user.registration.form.handler');
        $confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

        $process = $formHandler->process($confirmationEnabled);
        if ($process) {
            $user = $form->getData();

            $authUser = false;
            if ($confirmationEnabled) {
                $this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
                $route = 'fos_user_registration_check_email';
            } else {
                $authUser = true;
                $route = 'fos_user_registration_confirmed';
            }

            $this->setFlash('rz_user_success', 'registration.flash.user_created');

            $response = new RedirectResponse($this->container->get('router')->generate($route));

			$dispatcher = $this->container->get('event_dispatcher');
			$event = new RzUserEvent();
			$event->setUser($user);
			$dispatcher->dispatch(RzUserEvents::BEFORE_REGISTRATION_AUTH, $event);
			$this->authenticateUser($user, $response);
			$dispatcher->dispatch(RzUserEvents::AFTER_REGISTRATION_AUTH, $event);
    
            return $response;
        }

        $template = $this->container->get('rz_admin.template.loader')->getTemplates();
        return $this->container->get('templating')->renderResponse($template['rz_oauth.template.registration'], array('form' => $form->createView(),'template' => $template));
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        $template = $this->container->get('rz_admin.template.loader')->getTemplates();
        return $this->container->get('templating')->renderResponse($template['rz_oauth.template.registration_check_email'], array('user' => $user,'template' => $template));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction($token)
    {
        $user = $this->container->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setLastLogin(new \DateTime());

        $this->container->get('fos_user.user_manager')->updateUser($user);
        if ($redirectRoute = $this->container->getParameter('sonata.user.register.confirm.redirect_route')) {
            $response = new RedirectResponse($this->container->get('router')->generate($redirectRoute, $this->container->getParameter('sonata.user.register.confirm.redirect_route_params')));
        } else {
            $response = new RedirectResponse($this->container->get('router')->generate('fos_user_registration_confirmed'));
        }

        $this->authenticateUser($user, $response);

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }


        $template = $this->container->get('rz_admin.template.loader')->getTemplates();
        return $this->container->get('templating')->renderResponse($template['rz_oauth.template.registration_confirmed'], array('user' => $user,'template' => $template));
    }

    /**
     * Authenticate a user with Symfony Security
     *
     * @param \FOS\UserBundle\Model\UserInterface        $user
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function authenticateUser(UserInterface $user, Response $response)
    {
        try {
            $this->container->get('fos_user.security.login_manager')->loginUser(
                $this->container->getParameter('fos_user.firewall_name'),
                $user,
                $response);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
