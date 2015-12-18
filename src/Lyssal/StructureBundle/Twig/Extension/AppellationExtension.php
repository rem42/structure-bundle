<?php
namespace Lyssal\StructureBundle\Twig\Extension;

use Lyssal\StructureBundle\Appellation\AppellationManager;

/**
 * Fonctions Twig pour les appellations.
 * 
 * @author Rémi Leclerc
 */
class AppellationExtension extends \Twig_Extension
{
    /**
     * @var \Lyssal\StructureBundle\Appellation\AppellationManager AppellationManager
     */
    private $appellationManager;

    /**
     * Constructeur.
     * 
     * @param \Lyssal\StructureBundle\Appellation\AppellationManager $appellationManager AppellationManager
     */
    public function __construct(AppellationManager $appellationManager)
    {
        $this->appellationManager = $appellationManager;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('appellation', array($this, 'appellation'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('appellation_html', array($this, 'appellationHtml'), array('is_safe' => array('html'))),
            // Obsolète
            new \Twig_SimpleFunction('appellationHtml', array($this, 'appellationHtml'), array('is_safe' => array('html')))
        );
    }

    /**
     * Retourne l'appellation d'un objet.
     * 
     * @param object $object Objet
     * @return string Appellation de l'objet
     */
    public function appellation($object)
    {
        return $this->appellationManager->appellation($object);
    }
    
    /**
     * Retourne l'appellation en HTML d'un objet.
     * 
     * @param object $object Objet
     * @return string Appellation HTML de l'objet
     */
    public function appellationHtml($object)
    {
        return $this->appellationManager->appellationHtml($object);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'lyssal.structure.twig.extension.appellation';
    }
}
