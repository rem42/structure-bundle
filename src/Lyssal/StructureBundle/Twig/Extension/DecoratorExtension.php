<?php
namespace Lyssal\StructureBundle\Twig\Extension;

use Lyssal\StructureBundle\Decorator\DecoratorManager;

/**
 * Fonctions Twig pour les decorators.
 *
 * @author Rémi Leclerc
 */
class DecoratorExtension extends \Twig_Extension
{
    /**
     * @var \Lyssal\StructureBundle\Decorator\DecoratorManager DecoratorManager
     */
    private $decoratorManager;

    /**
     * Constructeur.
     *
     * @param \Lyssal\StructureBundle\Decorator\DecoratorManager $decoratorManager DecoratorManager
     */
    public function __construct(DecoratorManager $decoratorManager)
    {
        $this->decoratorManager = $decoratorManager;
    }


    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('decorator', array($this, 'decorator'))
        );
    }

    /**
     * Retourne le decorator d'un objet.
     *
     * @param object $object Objet
     * @return string Decorator
     */
    public function decorator($object)
    {
        return $this->decoratorManager->get($object);
    }


    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'lyssal.structure.twig.extension.decorator';
    }
}
