<?php
namespace Lyssal\StructureBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AppellationPass.
 * 
 * @author Rémi Leclerc
 */
class AppellationPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $appellationServices = $container->findTaggedServiceIds('appellation_handler');
        $appellationManagerService = $container->getDefinition('lyssal.appellation');

        foreach (array_keys($appellationServices) as $id)
            $appellationManagerService->addMethodCall('addAppellationHandler', array(new Reference($id)));
    }
}
