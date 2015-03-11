<?php
namespace Lyssal\StructureBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Lyssal\StructureBundle\DependencyInjection\CompilerPass\DecoratorPass;
use Lyssal\StructureBundle\DependencyInjection\CompilerPass\AppellationPass;

class LyssalStructureBundle extends Bundle
{
    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpKernel\Bundle\Bundle::build()
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DecoratorPass());
        $container->addCompilerPass(new AppellationPass());
    }
}
