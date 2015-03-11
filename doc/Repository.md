# EntityRepository

Le repository de `LyssalStructureBundle` peut servir de base à tous vos repository. Il définit différentes méthodes et permet de gérer simplement une pagination avec `PagerFanta`.



## Utilisation

Vous devez simplement étendre votre repository :

```php
namespace Acme\MonBundle\Repository;

use Lyssal\StructureBundle\Repository\EntityRepository;

/**
 * Repository de mon entité.
 */
class MonEntiteRepository extends EntityRepository
{
    
}
```

Dans votre entité, spécifiez votre Repository :

```php
namespace Acme\MonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mon entité.
 * 
 * @ORM\Entity(repositoryClass="\Acme\MonBundle\Repository\MonEntiteRepository")
 * @ORM\Table(name="acme_mon_entite")
 */
class MonEntite
{
    //...
}
```

Pour utiliser par défaut le Repository de `LyssalStructureBundle`, il suffit de définir `doctrine.orm.default_repository_class` ainsi :

```yml
doctrine:
    orm:
        default_repository_class: "Lyssal\StructureBundle\Repository\EntityRepository"
```


## Méthodes utilisables

Retourne le QueryBuilder pour la méthode findBy() :
```php
getQueryBuilderFindBy(array $conditions, array $orderBy = null, $limit = null, $offset = null)
```

Retourne le PagerFanta pour la méthode findBy() :
```php
getPagerFantaFindBy(array $conditions, array $orderBy = null, $nombreResultatsParPage = 20, $currentPage = 1)
```
