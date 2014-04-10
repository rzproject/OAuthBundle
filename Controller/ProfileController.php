<?php

namespace Rz\OAuthBundle\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
#use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sonata\UserBundle\Controller\ProfileFOSUser1Controller;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;


class ProfileController extends ProfileFOSUser1Controller
{
//
//    /**
//     * @return Response
//     *
//     * @throws AccessDeniedException
//     */
//    public function editProfileAction()
//    {
//
//        $user = $this->container->get('security.context')->getToken()->getUser();
//        if (!is_object($user) || !$user instanceof UserInterface) {
//            throw new AccessDeniedException('This user does not have access to this section.');
//        }
//
//        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
//        $dispatcher = $this->container->get('event_dispatcher');
//
//        $event = new GetResponseUserEvent($user, $this->getRequest());
//        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);
//
//        if (null !== $event->getResponse()) {
//            return $event->getResponse();
//        }
//
//        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
//        $formFactory = $this->container->get('sonata.user.profile.form.factory');
//
//        $form = $formFactory->createForm();
//        $form->setData($user);
//
//        $form->handleRequest($this->getRequest());
//
//        if ($form->isValid()) {
//            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
//            $userManager = $this->container->get('fos_user.user_manager');
//
//            $event = new FormEvent($form, $this->getRequest());
//            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
//
//            $userManager->updateUser($user);
//
//            if (null === $response = $event->getResponse()) {
//                $url = $this->container->get('router')->generate('sonata_user_profile_show');
//                $response = new RedirectResponse($url);
//            }
//
//            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $this->getRequest(), $response));
//
//            return $response;
//        }
//
//        return $this->render('SonataUserBundle:Profile:edit_profile.html.twig',array('form' => $form->createView(), 'user' => $user));
//    }
//
//    /**
//     * @param \Symfony\Component\HttpFoundation\Request $request
//     *
//     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
//     * @return Response
//     *
//     * @throws AccessDeniedException
//     */
//    public function editAuthenticationAction()
//    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
//        if (!is_object($user) || !$user instanceof UserInterface) {
//            throw new AccessDeniedException('This user does not have access to this section.');
//        }
//
//        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
//        $dispatcher = $this->container->get('event_dispatcher');
//
//        $event = new GetResponseUserEvent($user, $this->getRequest());
//        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);
//
//        if (null !== $event->getResponse()) {
//            return $event->getResponse();
//        }
//
//        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
//        $formFactory = $this->container->get('sonata.user.update_password.form.factory');
//
//        $form = $formFactory->createForm();
//        $form->setData($user);
//
//        $form->handleRequest($this->getRequest());
//
//        if ($form->isValid()) {
//            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
//            $userManager = $this->container->get('fos_user.user_manager');
//
//            $event = new FormEvent($form, $this->getRequest());
//            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
//
//            $userManager->updateUser($user);
//
//            if (null === $response = $event->getResponse()) {
//                $url = $this->container->get('router')->generate('sonata_user_profile_show');
//                $response = new RedirectResponse($url);
//            }
//
//            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $this->getRequest(), $response));
//
//            return $response;
//        }
//
//
//        return $this->render('SonataUserBundle:Profile:edit_authentication.html.twig', array(
//            'form' => $form->createView(),
//        ));
//    }
}
