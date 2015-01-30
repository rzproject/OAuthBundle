<?php

namespace Rz\OAuthBundle\Controller;

use Rz\UserBundle\Controller\ProfileSonataUserController as BaseProfileController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\FOSUserEvents;

class OAuthProfileController extends BaseProfileController
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

        $form = $this->container->get('rz.oauth.profile.form');
        $formHandler = $this->container->get('rz.oauth.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('rz_user_success', 'profile.flash.updated');

            return new RedirectResponse($this->generateUrl('fos_user_profile_show'));
        }

        $template = $this->container->get('rz_admin.template.loader')->getTemplates();

        return $this->render($template['rz_oauth.template.profile_edit'], array(
            'form'               => $form->createView(),
            'breadcrumb_context' => 'user_profile',
        ));
    }
}
