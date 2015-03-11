# DebrilRssAtomBundle


## Vues

Si vous utilisez `DebrilRssAtomBundle`, certaines vues peuvent être incluses dans les vôtres :

* `LyssalStructureBundle:DebrilRssAtom:feed.html.twig` with { 'feed':votreFluxRssDebril } : Affichage d'un flux
* `LyssalStructureBundle:DebrilRssAtom:items.html.twig` with { 'items':votreFluxRssDebril.items } : Affichage des items d'un flux
* `LyssalStructureBundle:DebrilRssAtom:item.html.twig` with { 'item':item } : Affichage d'un item

Exemple :
```php
public function rssAction()
{
    $fluxRss = $this->container->get('debril.reader')->getFeedContent('http://www.acme.fr/flux.rss');

    return $this->render('AcmeAppBundle:Rss:rss.html.twig', array('fluxRss' => $fluxRss));
}
```

```twig
{% include 'LyssalStructureBundle:DebrilRssAtom:feed.html.twig' with { 'feed':fluxRss } %}
```
