<?php
namespace Lyssal\StructureBundle\Decorator;

/**
 * Interface du manipulateur de Decorator.
 * 
 * @author Rémi Leclerc
 */
interface DecoratorHandlerInterface
{
    /**
     * Retourne si l'objet est supporté par le manipulateur de decorators.
     * 
     * @param object $object Objet dont il faut le décorator
     * @return boolean Vrai si l'objet a un manipulateur de décorator
     */
    public function supports($entity);
}
