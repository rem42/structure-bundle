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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Réponse de redirection
     */
    public function routeRedirect($route, $parameters = array())
    {
        return new RedirectResponse($this->router->generate($route, $parameters));
    }

    /**
     * Retourne un tableau JSON après une redirection.
     *
     * @param array $response Réponse JSON
     * @return \Symfony\Component\HttpFoundation\RedirectResponse Réponse de redirection
     */
    public function jsonRedirect(array $response)
    {
        return new RedirectResponse($this->getJsonUrl($response));
    }

    /**
     * Retourne l'URL d'une réponse JSON.
     *
     * @param array $response Réponse JSON
     * @return string URL
     */
    public function getJsonUrl(array $response)
    {
        return $this->router->generate('lyssal_structure_redirect_json', array('response' => urlencode(serialize($response))));
    }
}
