<?php

/*
 * This file is part of the RzOAuthBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\OAuthBundle\Controller;

use HWI\Bundle\OAuthBundle\Controller\ConnectController as BaseConnectController;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConnectController extends BaseConnectController
{
    public function completeRegistrationAction(Request $request, $key)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($hasUser) {
            throw new AccessDeniedException('Cannot connect already registered account.');
        }

        $session = $request->getSession();
        $error = $session->get('_hwi_oauth.registration_error.'.$key);
        $session->remove('_hwi_oauth.registration_error.'.$key);

        if (!($error instanceof AccountNotLinkedException) || (time() - $key > 300)) {
            // todo: fix this
            throw new \Exception('Cannot register an account.');
        }

        $userInformation = $this
            ->getResourceOwnerByName($error->getResourceOwnerName())
            ->getUserInformation($error->getRawToken())
        ;

        // enable compatibility with FOSUserBundle 1.3.x and 2.x
        if (interface_exists('FOS\UserBundle\Form\Factory\FactoryInterface')) {
            $form = $this->container->get('hwi_oauth.registration.form.factory')->createForm();
        } else {
            $form = $this->container->get('hwi_oauth.registration.form');
        }

        $formHandler = $this->container->get('hwi_oauth.registration.form.handler');
        if ($formHandler->process($request, $form, $userInformation)) {

            $this->container->get('hwi_oauth.account.connector')->connect($form->getData(), $userInformation);

            // Authenticate the user
            $this->authenticateUser($request, $form->getData(), $error->getResourceOwnerName(), $error->getRawToken());

            return $this->container->get('templating')->renderResponse('HWIOAuthBundle:Connect:registration_success.html.' . $this->getTemplatingEngine(), array(
                'userInformation' => $userInformation,
            ));
        }

        // reset the error in the session
        $key = time();
        $session->set('_hwi_oauth.registration_error.'.$key, $error);

        return $this->container->get('templating')->renderResponse('RzOAuthBundle:Registration:register_oauth.html.twig',
                                                                    array('key' => $key,
                                                                          'form' => $form->createView(),
                                                                          'userInformation' => $userInformation,
                                                                      ));
    }

    /**
     * Connects a user to a given account if the user is logged in and connect is enabled.
     *
     * @param Request $request The active request.
     * @param string  $service Name of the resource owner to connect to.
     *
     * @throws \Exception
     *
     * @return Response
     *
     * @throws NotFoundHttpException if `connect` functionality was not enabled
     * @throws AccessDeniedException if no user is authenticated
     */
    public function connectServiceAction(Request $request, $service)
    {
        $connect = $this->container->getParameter('hwi_oauth.connect');
        if (!$connect) {
            throw new NotFoundHttpException();
        }

        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!$hasUser) {
            throw new AccessDeniedException('Cannot connect an account.');
        }

        // Get the data from the resource owner
        $resourceOwner = $this->getResourceOwnerByName($service);

        $session = $request->getSession();
        $key = $request->query->get('key', time());

        if ($resourceOwner->handles($request)) {
            $accessToken = $resourceOwner->getAccessToken(
                $request,
                $this->generate('hwi_oauth_connect_service', array('service' => $service), true)
            );
            // save in session
            $session->set('_hwi_oauth.connect_confirmation.'.$key, $accessToken);
        } else {
            $accessToken = $session->get('_hwi_oauth.connect_confirmation.'.$key);
        }

        $userInformation = $resourceOwner->getUserInformation($accessToken);


        // Show confirmation page?
        if (!$this->container->getParameter('hwi_oauth.connect.confirmation')) {
            goto show_confirmation_page;
        }

        // Handle the form
        /** @var $form FormInterface */
        $form = $this->container->get('form.factory')
            ->createBuilder('form')
            ->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                show_confirmation_page:

                /** @var $currentToken OAuthToken */
                $currentToken = $this->container->get('security.context')->getToken();
                $currentUser  = $currentToken->getUser();

                $this->container->get('hwi_oauth.account.connector')->connect($currentUser, $userInformation);

                if ($currentToken instanceof OAuthToken) {
                    // Update user token with new details
                    $this->authenticateUser($request, $currentUser, $service, $currentToken->getRawToken(), false);
                }

                return $this->container->get('templating')->renderResponse('RzOAuthBundle:Connect:connect_success.html.' . $this->getTemplatingEngine(), array(
                    'userInformation' => $userInformation,
                ));
            }
        }

        return $this->container->get('templating')->renderResponse('RzOAuthBundle:Connect:connect_confirm.html.' . $this->getTemplatingEngine(), array(
            'key'             => $key,
            'service'         => $service,
            'form'            => $form->createView(),
            'userInformation' => $userInformation,
        ));
    }
}
