<?php
namespace Lyssal\StructureBundle\DependencyInjection;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * Service gérant les réponses aux actions des contrôleurs.
 *
 * @author Rémi Leclerc
 */
class Response
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface Router
     */
    private $router;


    /**
     * Constructeur.
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * Retourne un tableau JSON après une redirection.
     * @param array $response
     */
    public function redirectJson(array $response)
    {
        return new RedirectResponse($this->router->generate('lyssal_structure_redirect_json', array('response' => urlencode(serialize($response)))));
    }
}
