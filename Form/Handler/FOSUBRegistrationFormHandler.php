<?php

namespace Rz\OAuthBundle\Form\Handler;


use HWI\Bundle\OAuthBundle\Form\FOSUBRegistrationFormHandler as BaseFOSUBRegistrationFormHandler;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * FOSUBRegistrationFormHandler
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class FOSUBRegistrationFormHandler extends BaseFOSUBRegistrationFormHandler
{
    /**
     * Set user information from form
     *
     * @param UserInterface         $user
     * @param UserResponseInterface $userInformation
     *
     * @return UserInterface
     */
    protected function setUserInformation(UserInterface $user, UserResponseInterface $userInformation)
    {
        $user->setUsername($this->getUniqueUsername($userInformation->getNickname()));

        foreach($userInformation->getPaths() as $field=>$map) {
            $func = $this->camelize($field);
            $setter = 'set'.$func;
            $getter = 'get'.$func;
            if (method_exists($user, $setter)) {
                $user->{$setter}($userInformation->{$getter}());
            }
        }

        return $user;
    }

    /**
     * Camelizes a given string.
     *
     * @param  string $string Some string
     *
     * @return string The camelized version of the string
     */
    private function camelize($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $string);
    }
}
