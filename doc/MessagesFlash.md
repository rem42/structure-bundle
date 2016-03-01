# Messages Flash


## Le service FlashBag

Le service FlashBag de `LyssalStructureBundle` peut s'appeller de cette manière :

```php
$this->container->get('lyssal.flash_bag')->addFlash('success', 'Votre message nous a été envoyé, merci.');
```

Les méthodes de ce service sont :

* `getSessionFlashBag()` : Retourne le FlashBag
* `addFlash($type, $message)` : Crée un message Flash

Exemple :
```php
$this->addFlash('success', 'Votre message nous a été envoyé, merci.');
```

ou :
```php
$this->container->get('lyssal.flash_bag')->add('success', 'Votre message nous a été envoyé, merci.');
```


## Gabarits


### Foundation 6

Dans votre gabarit Twig, ajoutez cette ligne à l'endroit où vous souhaitez afficher vos alertes :
```twig
{% include 'LyssalStructureBundle:MessagesFlash:foundation_6.html.twig' %}
```

Les types de message sont :
* success
* warning
* alert (ou error)
* secondary (ou info)


### Foundation 5

Dans votre gabarit Twig, ajoutez cette ligne à l'endroit où vous souhaitez afficher vos alertes :
```twig
{% include 'LyssalStructureBundle:MessagesFlash:foundation_5.html.twig' %}
```

Les types de message sont :
* success
* warning
* info
* alert (ou error)
* secondary


### Bootstrap 3

Dans votre gabarit Twig, ajoutez cette ligne à l'endroit où vous souhaitez afficher vos alertes :
```twig
{% include 'LyssalStructureBundle:MessagesFlash:bootstrap_3.html.twig' %}
```

Les types de message sont :
* success
* info
* warning
* danger (ou error)
