<?php

/*
 * This file is part of the HWIOAuthBundle package.
 *
 * (c) Hardware.Info <opensource@hardware.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\OAuthBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseFOSUBUserProvider;


class FOSUBUserProvider extends BaseFOSUBUserProvider
{

    protected $completeRegistration;
    protected $tokenGenerator;

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        // Compatibility with FOSUserBundle < 2.0
        if (class_exists('FOS\UserBundle\Form\Handler\RegistrationFormHandler')) {
            return $this->userManager->loadUserByUsername($username);
        }

        return $this->userManager->findUserByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $property       = $this->getProperty($response);
        $getter         = 'get'.ucfirst($property);
        $property_value = $response->$getter();
        $params = array($this->getProperty($response) => $property_value);

        $user = $this->userManager->findUserBy($params);

        if (null === $user || null === $property_value) {
            if(!$this->completeRegistration) {
                $user = $this->createUser($response);
            } else {
                throw new AccountNotLinkedException(sprintf("User with '%s:%s' not found.", $property, $property_value));
            }
        }
        return $user;
    }


    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);

        foreach($response->getPaths() as $field=>$map) {
            if($field === 'username') {
                continue;
            }
            $func = $this->camelize($field);
            $setter = 'set'.$func;
            $getter = 'get'.$func;
            if (method_exists($user, $setter)) {
                $user->{$setter}($response->{$getter}());
            }
        }

        $username = $response->getUsername();

        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter(null);
            $this->userManager->updateUser($previousUser);
        }

        $this->userManager->updateUser($user);
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

    protected function createUser(UserResponseInterface $response) {

        $user = $this->userManager->createUser();
        $user->setPassword(substr($this->tokenGenerator->generateToken(), 0, 20));
        $user->setEnabled(true);

        foreach($response->getPaths() as $field=>$map) {
            $func = $this->camelize($field);
            $setter = 'set'.$func;
            $getter = 'get'.$func;
            if (method_exists($user, $setter)) {
                $user->{$setter}($response->{$getter}());
            }
        }
        $this->userManager->save($user);
        return $user;
    }

    /**
     * @return mixed
     */
    public function getCompleteRegistration()
    {
        return $this->completeRegistration;
    }

    /**
     * @param mixed $completeRegistration
     */
    public function setCompleteRegistration($completeRegistration = true)
    {
        $this->completeRegistration = $completeRegistration;
    }

    /**
     * @return mixed
     */
    public function getTokenGenerator()
    {
        return $this->tokenGenerator;
    }

    /**
     * @param mixed $tokenGenerator
     */
    public function setTokenGenerator($tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }
}
