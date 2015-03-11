<?php
namespace Lyssal\StructureBundle\Pagerfanta\View;

use Pagerfanta\View\DefaultView;
use Lyssal\StructureBundle\Pagerfanta\View\Template\FoundationTemplate;

/**
 * Rendu de la pagination PagerFanta pour Foundation.
 *
 * @author Rémi Leclerc
 */
class FoundationView extends DefaultView
{
    protected function createDefaultTemplate()
    {
        return new FoundationTemplate();
    }

    protected function getDefaultProximity()
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'foundation';
    }
}
