<?php
namespace Lyssal\StructureBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur utilisé pour les redirection.
 *
 * @author Rémi Leclerc
 * @Route("/Redirect")
 */
class ResponseController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
    /**
     * Retourne un objet JSON comme réponse.
     *
     * @param array $response Réponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse JSON
     * @Route("/Json", name="lyssal_structure_redirect_json")
     * @Method({ "GET" })
     */
    public function jsonAction(Request $request)
    {
        return new JsonResponse(unserialize(urldecode($request->query->get('response'))));
    }
}
