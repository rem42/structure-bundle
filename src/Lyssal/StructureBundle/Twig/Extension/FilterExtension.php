<?php
namespace Lyssal\StructureBundle\Twig\Extension;

/**
 * Ajout de filtres Twig.
 * 
 * @author Rémi Leclerc
 */
class FilterExtension extends \Twig_Extension
{
    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array
        (
            'raw_secure' => new \Twig_Filter_Method($this, 'rawSecure', array('is_safe' => array('html'))),
            // Obsolète
            'rawSecure' => new \Twig_Filter_Method($this, 'rawSecure', array('is_safe' => array('html')))
        );
    }

    /**
     * Similaire au filtre raw mais plus sécurisé.
     *
     * @param string $html HTML
     * @return string HTML
     */
    public function rawSecure($html)
    {
        $balisesSupprimees = array('applet', 'embed', 'frameset', 'head', 'iframe', 'noembed', 'noframes', 'noscript', 'object', 'script', 'style');
        
        $balisesSupprimeesRegex = array();
        foreach ($balisesSupprimees as $baliseSupprimee) {
            $balisesSupprimeesRegex[] = '@<'.$baliseSupprimee.'[^>]*?>.*?</'.$baliseSupprimee.'>@siu';
        }

        return preg_replace($balisesSupprimeesRegex, '', $html);
    }
    
    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'lyssal.structure.twig.extension.filter';
    }
}
