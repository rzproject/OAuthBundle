<?php



namespace Rz\OAuthBundle\Form\Handler;


use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGenerator;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Form\FOSUBRegistrationFormHandler as BaseHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationFormHandler extends BaseHandler
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
