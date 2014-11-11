<?php

/*
 * This file is part of the RzOAuthBundle package.
 *
 * (c) mell m. zamora <mell@rzproject.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Rz\OAuthBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class RzOAuthExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        #TODO:save for future implentation
        $loader->load('profile.xml');
        $this->configureProfile($config, $container);
        $loader->load('registration.xml');
        $this->configureRegistration($config, $container);
        $this->configureRzTemplates($config, $container);
        $this->configureListener($config, $container);
        $loader->load('listeners.xml');

    }


    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureRegistration(array $config, ContainerBuilder $container)
    {
        $container->setParameter('rz.oauth.registration.form.options', array());
        $container->setParameter('rz.oauth.registration.form.type', $config['registration']['form']['type']);
        $container->setParameter('rz.oauth.registration.form.name', $config['registration']['form']['name']);
        $container->setParameter('rz.oauth.registration.form.validation_groups', $config['registration']['form']['validation_groups']);

        $container->setAlias('rz.oauth.registration.form.handler', $config['registration']['form']['handler']);

        $container->setParameter('rz.oauth.registration.form.options', array());

        $container->setParameter('rz.oauth.user.provider.fosub_bridge.class', $config['registration']['fos_user_bridge_class']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureProfile(array $config, ContainerBuilder $container)
    {
        $container->setParameter('rz.oauth.profile.form.type', $config['profile']['form']['type']);
        $container->setParameter('rz.oauth.profile.form.name', $config['profile']['form']['name']);
        $container->setParameter('rz.oauth.profile.form.validation_groups', $config['profile']['form']['validation_groups']);

        $container->setAlias('rz.oauth.profile.form.handler', $config['profile']['form']['handler']);

        $container->setParameter('rz.oauth.profile.form.options', array());
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureRzTemplates($config, ContainerBuilder $container)
    {
        $container->setParameter('rz_oauth.templates', $config['templates']);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function configureListener($config, ContainerBuilder $container)
    {
        $container->setParameter('rz.oauth.login_listener.class', $config['login_listener_class']);
    }
}
