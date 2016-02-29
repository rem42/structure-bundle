# Decorator

Les `Decorator` permettent de créer des méthodes spécifiques à votre entité sans les avoir à les ajouter ni dans votre classe `Entity` ni dans votre `Manager`. Contrairement aux `Entity`, les `Decorator` peuvent utiliser des services mais n'ont pas vocation à lancer des appels vers la base de données contrairement aux `Manager`.


## Création

Le `Decorator` de votre entité :

```php
namespace Acme\MonBundle\Decorator;

use Lyssal\StructureBundle\Decorator\DecoratorHandler;
use Lyssal\StructureBundle\Decorator\DecoratorHandlerInterface;
use Acme\MonBundle\Entity\MonEntite;

class MonEntiteDecorator extends DecoratorHandler implements DecoratorHandlerInterface
{
    /**
     * (non-PHPdoc)
     * @see \Lyssal\StructureBundle\Decorator\DecoratorHandlerInterface::supports()
     */
    public function supports($entity)
    {
        return ($entity instanceof MonEntite);
    }


    /**
     * Retourne la balise HTML de l'icône 16px.
     * 
     * @return string Icône en HTML
     */
    public function getStatutLibelle()
    {
        return $this->entity->getStatut()->__toString();
    }
}
```

Créez ensuite votre service :

```xml
<service id="acme.monbundle.decorator.mon_entite" class="Acme\MonBundle\Decorator\MonEntiteDecorator">
    <argument type="service" id="router" />
    <argument type="service" id="lyssal.decorator" />
    <tag name="decorator_handler" />
</service>
```


## Utilisation

En utilisant le service :
```php
$monEntiteDecorator = $this->container->get('lyssal.decorator')->get($monEntite);
echo $monEntiteDecorator->getStatutLibelle();
```

Les décorators fonctionnent également avec des tableaux d'entités :
```php
$mesEntitesDecorators = $this->container->get('lyssal.decorator')->get($mesEntites);
foreach ($mesEntitesDecorators as $monEntiteDecorator)
    echo $monEntiteDecorator->getStatutLibelle();
```

Les `Decorator` peuvent avoir plusieurs vocations :

* Retourner une URL permettant de visualiser l'entité :
```php
$monDecorator->getUrl();
```
* Retourner du code HTML si par exemple une image est liée à l'entité :
```php
$monDecorator->getIconeHtml();
```
* Vérifier un droit, un accès :
```php
if ($periodeDecorator->estOuverte()) {}
```
* Toutes sortes de traitement ne nécessitant pas de requêtes :
```php
if ($periodeDecorator->dateOuvertureEstTerminee()) {}
echo $periodeDecorator->getNombreJours();
// Etc
```

Enfin, si votre entité à une relation, le mutateur renverra automatiquement un `Decorator` :
```php
$monEntiteDecorator = $this->container->get('lyssal.decorator')->get($monEntite);
$monEntiteDecorator->getTypes(); // Renverra un tableau de `Decorator` si `MonEntiteTypeDecorator` existe
```

### Fonction Twig

Dans votre vue Twig, vous pouvez également créer un `Decorator` avec la fonction `decorator(entity)`.

Par exemple :
```yaml
{{ decorator(app.user).avatarHtml|raw_secure }}
```
