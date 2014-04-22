<?php

namespace Rz\OAuthBundle\Controller;

use Rz\UserBundle\Controller\ProfileController as BaseProfileController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\FOSUserEvents;

class ProfileController extends BaseProfileController
{
    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function editProfileAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('sonata.user.profile.form');
        $formHandler = $this->container->get('sonata.user.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('sonata_user_success', 'profile.flash.updated');

            return new RedirectResponse($this->generateUrl('sonata_user_profile_show'));
        }

        return $this->render('RzOAuthBundle:Profile:edit_profile.html.twig', array(
            'form'               => $form->createView(),
            'user'               => $user,
            'breadcrumb_context' => 'user_profile',
        ));
    }
}
