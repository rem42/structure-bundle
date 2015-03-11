<?php
namespace Lyssal\StructureBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * DecoratorPass.
 * 
 * @author Rémi Leclerc
 */
class DecoratorPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $appellationServices = $container->findTaggedServiceIds('decorator_handler');
        $appellationManagerService = $container->getDefinition('lyssal.decorator');

        foreach (array_keys($appellationServices) as $id)
            $appellationManagerService->addMethodCall('addDecoratorHandler', array(new Reference($id)));
    }
}
