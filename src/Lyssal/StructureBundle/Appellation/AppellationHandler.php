<?php
namespace Lyssal\StructureBundle\Appellation;

use Symfony\Component\Routing\RouterInterface;

/**
 * Manipulateur parent de Appellation.
 * 
 * @author Rémi Leclerc
 */
abstract class AppellationHandler
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface $router
     */
    protected $router;
    
    /**
     * Constructeur du manipulateur de Appellatoin.
     *
     * @param \Symfony\Component\Routing\RouterInterface $router Router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
}
