# Messages Flash


## Foundation 5

Créez un message Flash dans votre contrôleur de cette manière :
```php
$this->addFlash('success', 'Votre message nous a été envoyé, merci.');
```

ou :
```php
$this->container->get('session')->getFlashBag()->add('success', 'Votre message nous a été envoyé, merci.');
```

Dans votre gabarit Twig, ajoutez cette ligne à l'endroit où vous souhaitez afficher votre alerte :
```twig
{% include 'LyssalStructureBundle:MessagesFlash:foundation_5.html.twig' %}
```

Les types de message sont :
* success
* warning
* info
* alert
* secondary

## Bootstrap 3

Créez un message Flash dans votre contrôleur de cette manière :
```php
$this->addFlash('danger', 'Une erreur est apparue.');
```

ou :
```php
$this->container->get('session')->getFlashBag()->add('danger', 'Une erreur est apparue.');
```

Dans votre gabarit Twig, ajoutez cette ligne à l'endroit où vous souhaitez afficher votre alerte :
```twig
{% include 'LyssalStructureBundle:MessagesFlash:bootstrap_3.html.twig' %}
```

Les types de message sont :
* success
* info
* warning
* danger
