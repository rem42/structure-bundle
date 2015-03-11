# Appellation

Les appellations permettent d'afficher l'appellation d'un objet, c'est-à-dire une chaîne de caractères qui définit l'objet.

Les méthodes sont :

* `appellation($objet)` : Par défaut, équivalent à l'appel de __toString()
* `appellationHtml($objet)` : Par défaut, équivalent à l'appel de appellation()

Le but de `appellation($objet)` est de retourner une simple chaîne de caractères qui définira l'objet. Par exemple, `appellation($utilisateur)` pourrait renvoyer `M. Rémi Leclerc`.

Le but de `appellationHtml($objet)` est de retourner le contenu HTML qui définira l'objet et qui peut contenir de la mise en forme ou un lien. Par exemple, `appellation($ville)` pourrait renvoyer `<a href="/Villes/Paris">Paris</a>`.

Ces fonctions vous aideront à garder une cohérence dans l'ensemble des appellations de votre application.


## Utilisation

En utilisant le service :
```php
$appellationVille = $this->container->get('lyssal.appellation')->appellation($ville);
```

Dans une vue Twig :
```twig
<p>Bonjour {{ appellation(utilisateur) }}</p>

<p>Cliquez sur la ville : {{ appellationHtml(ville) }}.</p>
```


## Personnaliser l'appellation de votre entité

Si vous souhaitez par exemple personnaliser l'appellation de `AcmeMonBundle:Entite`, créez le fichier `EntiteAppellation.php` dans un dossier `Appellation` à la racine de votre bundle :

```php
<?php
namespace Acme\MonBundle\Appellation;

use Lyssal\StructureBundle\Appellation\AppellationHandlerInterface;
use Lyssal\StructureBundle\Appellation\AppellationHandler;
use Acme\MonBundle\Entity\Entite;

class EntiteAppellation extends AppellationHandler implements AppellationHandlerInterface
{
    /**
     * (non-PHPdoc)
     * @see \Lyssal\StructureBundle\Appellation\AppellationHandlerInterface::supports()
     */
    public function supports($object)
    {
        return ($object instanceof Entite);
    }

    /**
     * (non-PHPdoc)
     * @see \Lyssal\StructureBundle\Appellation\AppellationHandlerInterface::appellation()
     */
    public function appellation($object)
    {
        return $object->__toString().' (#'.$object->getId().')';
    }

    /**
     * (non-PHPdoc)
     * @see \Lyssal\StructureBundle\Appellation\AppellationHandlerInterface::appellationHtml()
     */
    public function appellationHtml($object)
    {
        return '<a href="'.$this->router->generate('acme_monbundle_entite_view', array('entite' => $object->getId())).'">'.$this->appellation($object).'</a>';
    }
}
```

Créez ensuite votre service :

```xml
<service id="acme.monbundle.appellation.entite" class="Acme\MonBundle\Appellation\EntiteAppellation">
    <argument type="service" id="router" />
    <tag name="appellation_handler" />
</service>
```
