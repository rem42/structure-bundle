<?php
namespace Lyssal\StructureBundle\Appellation;

use Lyssal\StructureBundle\Decorator\DecoratorHandlerInterface;
/**
 * Manager de Appellation.
 * 
 * @author Rémi Leclerc
 */
class AppellationManager
{
    /**
     * @var \Lyssal\StructureBundle\Appellation\AppellationHandlerInterface[]
     */
    private $appellationHandlers = array();
    
    /**
     * Ajoute un manipulateur d'appellation.
     * 
     * @param \Lyssal\StructureBundle\Appellation\AppellationHandlerInterface $appellationHandler Manipulateur
     */
    public function addAppellationHandler(AppellationHandlerInterface $appellationHandler)
    {
        $this->appellationHandlers[] = $appellationHandler;
    }
    
    /**
     * Retourne l'appellation d'un objet.
     * 
     * @param object $object Objet
     * @throw \Exception Si l'objet n'a pas de manipulateur et n'est pas un objet
     * @throw \Exception Si l'objet n'a pas de manipulateur et n'a pas de méthode __toString
     * @return string Appellation de l'objet
     */
    public function appellation($object)
    {
        foreach ($this->appellationHandlers as $appellationHandler)
        {
            if ($appellationHandler->supports($object))
                return $appellationHandler->appellation($object);
        }
        
        if (!is_object($object))
        {
            throw new \Exception('Le paramètre pour l\'appellation n\'est pas un objet.');
        }
        elseif (!method_exists($object, '__toString'))
        {
            if ($object instanceof DecoratorHandlerInterface)
                return $this->appellation($object->getEntity());
            
            throw new \Exception('Le paramètre "'.get_class($object).'" ne possède pas de méthode __toString().');
        }
        
        return $object->__toString();
    }
    
    /**
     * Retourne l'appellation en HTML d'un objet.
     * 
     * @param object $object Objet
     * @return string Appellation HTML de l'objet
     */
    public function appellationHtml($object)
    {
        foreach ($this->appellationHandlers as $appellationHandler)
        {
            if ($appellationHandler->supports($object))
                return $appellationHandler->appellationHtml($object);
        }

        return $this->appellation($object);
    }
}
