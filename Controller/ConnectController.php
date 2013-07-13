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
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $session = $request->getSession();
        $error = $session->get('_hwi_oauth.registration_error.'.$key);
        $session->remove('_hwi_oauth.registration_error.'.$key);

        if ($hasUser || !($error instanceof AccountNotLinkedException) || (time() - $key > 800)) {
            // todo: fix this
            throw new \Exception('Cannot register an account.');
        }

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $form = $this->container->get('hwi_oauth.registration.form.factory')->createForm();
        $form->setData($user);

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
            try {
                $this->container->get('hwi_oauth.account.connector')->connect($form->getData(), $userInformation);
//                $route = 'fos_user_registration_confirmed';
//                $url = $this->container->get('router')->generate($route);
//                $response = new RedirectResponse($url);
                // Authenticate the user
                $this->authenticateUser($form->getData(), $error->getResourceOwnerName(), $error->getRawToken());

                return $this->container->get('templating')->renderResponse('HWIOAuthBundle:Connect:registration_success.html.' . $this->getTemplatingEngine(), array(
                                                                                                                                                                 'userInformation' => $userInformation,
                                                                                                                                                             ));

            } catch (\Exception $e) {
                throw $e;
            }
        }

        // reset the error in the session
        $key = time();
        $session->set('_hwi_oauth.registration_error.'.$key, $error);

        return $this->container->get('templating')->renderResponse('RzOAuthBundle:Registration:register_oauth.html.twig', array(
                                                                                                                                  'key' => $key,
                                                                                                                                  'form' => $form->createView(),
                                                                                                                                  'userInformation' => $userInformation,
                                                                                                                              ));
    }
}
