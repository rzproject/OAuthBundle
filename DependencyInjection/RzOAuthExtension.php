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
        $loader->load('listeners.xml');
        $loader->load('registration.xml');
        $loader->load('profile.xml');
        $loader->load('services.xml');

        #TODO:save for future implentation
        //$loader->load('registration.xml');
        //$this->loadRegistrationSettings($config['registration'], $container);
        $this->configureProfile($config, $container);
        $this->configureFOSUB($config, $container);
    }

    /**
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
//    public function loadRegistrationSettings($config, ContainerBuilder $container)
//    {
//        $container->setParameter('fos_user.registration.form.type', $config['form']['type']);
//        $container->setParameter('fos_user.registration.form.name', $config['form']['name']);
//        $container->setParameter('fos_user.registration.form.validation_groups', $config['form']['validation_groups']);
//    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureProfile(array $config, ContainerBuilder $container)
    {
        $container->setParameter('rz_o_auth.profile.form.type', $config['profile']['form']['type']);
        $container->setParameter('rz_o_auth.profile.form.name', $config['profile']['form']['name']);
        $container->setParameter('rz_o_auth.profile.form.validation_groups', $config['profile']['form']['validation_groups']);
        //$container->setParameter('sonata.user.configuration.profile_blocks', $config['profile']['dashboard']['blocks']);
        $container->setAlias('rz_o_auth.profile.form.handler', $config['profile']['form']['handler']);
        $container->setParameter('rz_o_auth.profile.form.options', array());
    }

    public function configureFOSUB(array $config, ContainerBuilder $container) {
        if (isset($config['fosub'])) {
            $container
                ->setDefinition('rz_o_auth.user.provider.fosub_bridge', new DefinitionDecorator('rz_o_auth.user.provider.fosub_bridge.def'))
                ->addArgument($config['fosub']['properties'])
            ;

            $container->setAlias('hwi_oauth.account.connector', 'rz_o_auth.user.provider.fosub_bridge');
        }
    }
}
