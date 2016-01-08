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
     * Redirige l'internaute vers une route.
     *
     * @param string                $route      Route
     * @param array<string, string> $parameters Paramètres de la route
     */
    public function routeRedirect($route, $parameters = array())
    {
        return new RedirectResponse($this->router->generate($route, $parameters));
    }

    /**
     * Retourne un tableau JSON après une redirection.
     *
     * @param array $response
     */
    public function jsonRedirect(array $response)
    {
        return $this->routeRedirect('lyssal_structure_redirect_json', array('response' => urlencode(serialize($response))));
    }
}
