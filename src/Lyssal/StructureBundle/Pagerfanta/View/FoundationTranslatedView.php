<?php
namespace Lyssal\StructureBundle\Pagerfanta\View;

use WhiteOctober\PagerfantaBundle\View\TranslatedView;

/**
 * Rendu de la pagination PagerFanta avec les textes traduits pour Foundation.
 *
 * @author Rémi Leclerc
 */
class FoundationTranslatedView extends TranslatedView
{
    protected function previousMessageOption()
    {
        return 'prev_message';
    }

    protected function nextMessageOption()
    {
        return 'next_message';
    }

    protected function buildPreviousMessage($text)
    {
        return sprintf('&laquo; %s', $text);
    }

    protected function buildNextMessage($text)
    {
        return sprintf('%s &raquo;', $text);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'foundation_translated';
    }
}
