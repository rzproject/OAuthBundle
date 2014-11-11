<?php


namespace Rz\OAuthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        //override User Admin
        $definition = $container->getDefinition('hwi_oauth.user.provider.fosub_bridge');
        $definition->setClass($container->getParameter('rz.oauth.user.provider.fosub_bridge.class'));

    }
}
