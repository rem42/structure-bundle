<?php
namespace Lyssal\StructureBundle\Decorator;

/**
 * Manager de Decorator.
 * 
 * @author Rémi Leclerc
 */
class DecoratorManager
{
    /**
     * @var \Lyssal\StructureBundle\Decorator\DecoratorHandlerInterface[]
     */
    private $decoratorHandlers = array();
    
    /**
     * Ajoute un manipulateur de decorators.
     * 
     * @param \Lyssal\StructureBundle\Decorator\DecoratorHandlerInterface $decoratorHandler Manipulateur
     */
    public function addDecoratorHandler(DecoratorHandlerInterface $decoratorHandler)
    {
        $this->decoratorHandlers[] = $decoratorHandler;
    }
    
    /**
     * Retourne si l'entité est supportée par le manager Decorator.
     * 
     * @param object $entity L'entité
     * @return boolean VRAI ssi supportée
     */
    public function isSupportedEntity($entity)
    {
        if (is_array($entity) || $entity instanceof \ArrayIterator || $entity instanceof \Doctrine\ORM\PersistentCollection)
        {
            if (count($entity) == 0)
                return false;
            
            foreach ($entity as $uneEntite)
            {
                if (!$this->isSupportedEntity($uneEntite))
                    return false;
            }
            return true;
        }
        
        foreach ($this->decoratorHandlers as $decoratorHandler)
        {
            if ($decoratorHandler->supports($entity))
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Retourne le decorator d'une entité.
     *
     * @param object $entity Entité
     * @throw \Exception Si l'objet n'est pas un objet
     * @throw \Exception Si l'objet n'a pas de decorator
     * @return string Decorator de l'entité
     */
    public function get($entity)
    {
        if (is_array($entity) || $entity instanceof \ArrayIterator || $entity instanceof \Doctrine\ORM\PersistentCollection)
        {
            $decorators = array();
            foreach ($entity as $uneEntite)
                $decorators[] = $this->get($uneEntite);
            return $decorators;
        }
        
        foreach ($this->decoratorHandlers as $decoratorHandler)
        {
            if ($decoratorHandler->supports($entity))
            {
                // Clone pour éviter les références et retourner les mêmes objets
                $decoratorHandlerCopie = clone $decoratorHandler;
                $decoratorHandlerCopie->setEntity($entity);
                return $decoratorHandlerCopie;
            }
        }
    
        if (!is_object($entity))
        {
            throw new \Exception('Le paramètre pour le decorator n\'est pas un objet.');
        }
        else
        {
            throw new \Exception('Le paramètre "'.get_class($entity).'" ne possède pas de decorator.');
        }
    }
}
