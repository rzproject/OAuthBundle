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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('rz_o_auth');
        #TODO:save for future implentation
        $this->addBundleSettings($node);
        $this->addTemplates($node);
        return $treeBuilder;
    }

    private function addBundleSettings(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('login_listener_class')->defaultValue('Rz\\OAuthBundle\\Event\\Listener\\OAuthLoginEventListener')->end()
                ->scalarNode('force_complete_registration')->defaultValue(true)->end()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('fos_user_bridge_class')->defaultValue('Rz\\OAuthBundle\\Security\\Core\\User\\FOSUBUserProvider')->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('rz_oauth_user_registration')->end()
                                ->scalarNode('handler')->defaultValue('rz.oauth.registration.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('rz_oauth_user_registration_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('Registration', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('profile')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('rz_oauth_user_profile')->end()
                                ->scalarNode('handler')->defaultValue('rz.oauth.profile.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('rz_oauth_user_profile_form')->end()
                                ->arrayNode('validation_groups')
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('Profile', 'Default'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     *
     * @return void
     */
    private function addTemplates(ArrayNodeDefinition $node)
    {
        //TODO: add other templates for configuration
        $node
            ->children()
                ->arrayNode('templates')
                        ->addDefaultsIfNotSet()
                        ->canBeUnset()
                        ->children()
                            ->scalarNode('layout')->defaultValue('RzOAuthBundle::layout.html.twig')->end()
                            ->scalarNode('login')->defaultValue('RzOAuthBundle:OAuthSecurity:login.html.twig')->end()
                            ->scalarNode('profile')->defaultValue('RzUserBundle:Profile:show.html.twig')->end()
                            ->scalarNode('profile_action')->defaultValue('RzUserBundle:Profile:action.html.twig')->end()
                            ->scalarNode('profile_edit')->defaultValue('RzUserBundle:Profile:edit_profile.html.twig')->end()
                            ->scalarNode('profile_edit_authentication')->defaultValue('RzUserBundle:Profile:edit_authentication.html.twig')->end()
                            ->scalarNode('registration')->defaultValue('RzOAuthBundle:OAuthRegistration:register.html.twig')->end()
                            ->scalarNode('registration_content')->defaultValue('RzUserBundle:Registration:register_content.html.twig')->end()
                            ->scalarNode('registration_oauth')->defaultValue('RzOAuthBundle:OAuthRegistration:register_oauth.html.twig')->end()
                            ->scalarNode('registration_oauth_content')->defaultValue('RzUserBundle:Registration:register_oauth_content.html.twig')->end()
                            ->scalarNode('registration_check_email')->defaultValue('RzUserBundle:Registration:check_email.html.twig')->end()
                            ->scalarNode('registration_confirmed')->defaultValue('RzUserBundle:Registration:confirmed.html.twig')->end()
                            ->scalarNode('registration_email')->defaultValue('RzUserBundle:Registration:email.html.twig')->end()
                            ->scalarNode('change_password')->defaultValue('RzUserBundle:ChangePassword:change_password.html.twig')->end()
                            ->scalarNode('change_password_content')->defaultValue('RzUserBundle:ChangePassword:change_password_content.html.twig')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
