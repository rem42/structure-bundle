<?php
/**
 * Classe parente pour créer des helpers.
 * 
 * @author Rémi Leclerc
 */
namespace Lyssal\StructureBundle\Decorator;

use Symfony\Component\Routing\RouterInterface;

abstract class DecoratorHandler
{
    /**
     * \Symfony\Component\Routing\RouterInterface Router
     */
    protected $router;

    /**
     * \Lyssal\StructureBundle\Decorator\DecoratorManager DecoratorManager
     */
    protected $decoratorManager;
    
    /**
     * @var object Entité
     */
    protected $entity;
    
    
    /**
     * Constructeur du decorator.
     * 
     * @param \Symfony\Component\Routing\RouterInterface $router Router
     * @param \Lyssal\StructureBundle\Decorator\DecoratorManager $decoratorManager DecoratorManager
     */
    public function __construct(RouterInterface $router, DecoratorManager $decoratorManager)
    {
        $this->router = $router;
        $this->decoratorManager = $decoratorManager;
    }
    
    /**
     * Affecte l'entité au décorator.
     * 
     * @param object $entity Entité
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    /**
     * Retourne l'entité.
     *
     * @return object Entité
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    /**
     * Accesseurs et mutateurs.
     * 
     * @param string $name Nom de la fonction
     * @param unknown $args Arguments de la méthode
     * @throws \Exception Si la méthode n'est pas trouvée
     * @return mixed Retour de la méthode appelée
     */
    public function __call($name, $args)
    {
        if (method_exists($this->entity, $name))
            return call_user_func_array(array($this->entity, $name), $args);
        elseif (method_exists($this->entity, 'get'.ucfirst($name)))
            return call_user_func_array([$this->entity, 'get'.ucfirst($name)], $args);
        elseif (method_exists($this->entity, 'is'.ucfirst($name)))
            return call_user_func_array([$this->entity, 'is'.ucfirst($name)], $args);
        else throw new \Exception('La méthode "'.$name.'" n\'existe pas pour l\'objet "'.get_class($this->entity).'".');
    }
}
