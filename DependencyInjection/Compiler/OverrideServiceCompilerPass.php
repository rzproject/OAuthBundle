<?php


namespace Rz\OAuthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        #####################
        # override FOSUBUser
        #####################
        $definition = $container->getDefinition('hwi_oauth.user.provider.fosub_bridge');
        $definition->setClass($container->getParameter('rz.oauth.user.provider.fosub_bridge.class'));
        $definition->addMethodCall('setCompleteRegistration', array($container->getParameter('rz.oauth.force_complete_registration')));
        $definition->addMethodCall('setTokenGenerator', array(new Reference('fos_user.util.token_generator')));

    }
}
