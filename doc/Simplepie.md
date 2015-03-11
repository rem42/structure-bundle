# Simplepie


## Vues

Si vous utilisez `Simplepie`, certaines vues peuvent être incluses dans les vôtres :

* `LyssalStructureBundle:Simplepie:feed.html.twig` with { 'feed':votreFluxRss } : Affichage d'un flux
* `LyssalStructureBundle:Simplepie:items.html.twig` with { 'items':votreFluxRss.items } : Affichage des items d'un flux
* `LyssalStructureBundle:Simplepie:item.html.twig` with { 'item':item } : Affichage d'un item

Exemple :
```php
public function rssAction()
{
    $fluxRss = $this->container->get('fkr_simple_pie.rss');
    $fluxRss->set_feed_url('http://www.acme.fr/flux.rss');
    $fluxRss->enable_order_by_date();
    $fluxRss->init();

    return $this->render('AcmeAppBundle:Rss:rss.html.twig', array('fluxRss' => $fluxRss));
}
```

```twig
{% include 'LyssalStructureBundle:Simplepie:feed.html.twig' with { 'feed':fluxRss } %}
```
