<?php
namespace Lyssal\StructureBundle\Appellation;

/**
 * Interface du manipulateur de Appellation.
 * 
 * @author Rémi Leclerc
 */
interface AppellationHandlerInterface
{
    /**
     * Retourne si l'objet est supporté par le manipulateur d'appellation.
     * 
     * @param object $object Objet dont il faut l'appellation
     * @return boolean Vrai si l'objet a un manipulateur d'appellation
     */
    public function supports($object);

    /**
     * Retourne l'appellation de l'objet.
     *
     * @param object $object Objet dont il faut l'appellation
     * @return string Appellation de l'objet
     */
    public function appellation($object);

    /**
     * Retourne l'appellation HTML de l'objet.
     *
     * @param object $object Objet dont il faut l'appellation
     * @return string Appellation HTML de l'objet
     */
    public function appellationHtml($object);
}
