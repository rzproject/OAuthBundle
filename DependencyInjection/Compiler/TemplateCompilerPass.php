<?php



namespace Rz\OAuthBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TemplateCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('rz_admin.template.loader');
        $templates = $container->getParameter('rz_oauth.templates');
        $rzuserTemplates = array();
        foreach($templates as $key => $template) {
            $rzuserTemplates[sprintf('rz_oauth.template.%s', $key)] = $template;
        }
        $definition->addMethodCall('setTemplates', array($rzuserTemplates));
    }
}
