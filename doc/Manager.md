# Manager

Le manager de `LyssalStructureBundle` peut servir de base à tous vos manager. Il définit différentes méthodes.


## Utilisation

Vous devez simplement étendre votre manager :

```php
use Lyssal\StructureBundle\Manager\Manager;

/**
 * Manager de mon entité.
 */
class EntiteManager extends Manager
{
    
}
```

## Méthodes utilisables

Retourne un tableau d'entités :
```php
findBy(array $conditions, array $orderBy = null, $limit = null, $offset = null, $extras = array())
```

Retourne un tableau d'entités en utilisant des %LIKE% :
```php
findLikeBy(array $conditions, array $orderBy = null, $limit = null, $offset = null)
```

Retourne une entité :
```php
findOneBy(array $conditions)
```

Retourne une entité avec son identifiant :
```php
findOneById($id)
```

Retourne toutes les entités :
```php
findAll()
```

Retourne le nombre de lignes en base :
```php
count()
```

Crée une nouvelle entité :
```php
create()
```

Enregistre une ou plusieurs entités :
```php
save($entites)
```

Persiste une ou plusieurs entités :
```php
persist($entites)
```

Flush :
```php
flush()
```

Détache tous les objets :
```php
clear()
```

Supprime une ou plusieurs entités :
```php
remove($entites)
```

Supprime toutes les entités :
```php
removeAll($initAutoIncrement)
```

Vérifie si une entité existe :
```php
exists($entity)
```

Effectue un TRUNCATE sur la table :
```php
truncate($initAutoIncrement)
```

Spécifie le nouveau AUTO_INCREMENT de l'identifiant de la table à 1 :
```php
initAutoIncrement()
```

Spécifie le nouveau AUTO_INCREMENT de l'identifiant de la table :
```php
setAutoIncrement($initAutoIncrement)
```

Retourne le nom de la table en base de données :
```php
getTableName()
```

Retourne le nom des identifiants de l'entité :
```php
getIdentifier()
```

Retourne si l'entité gérée possède un champ :
```php
hasField($fieldName)
```

Retourne si l'entité gérée possède une association :
```php
hasAssociation($fieldName)
```

### Paramètre $extras

Exemple d'utilisation de `$conditions` pour le manager (fictif) `VilleManager` :
```php
// (genre = $genre OR genreParent = $genre) AND genre.nom LIKE '%tratégi%'
$conditions = array
(
    EntityRepository::OR_WHERE => array
    (
        'genre' => $genre,
        'genreParent' => $genre
    ),
    EntityRepository::WHERE_LIKE => array
    (
        'genre.nom' => '%tratégi%'
    )
);
// (genre.nom LIKE '%tratégi%' OR genre.nom LIKE '%éflexio%')
$conditions = array
(
    EntityRepository::OR_WHERE => array
    (
        array(EntityRepository::WHERE_LIKE => array('genre.nom' => '%tratégi%')),
        array(EntityRepository::WHERE_LIKE => array('genre.nom' => '%éflexio%'))
    )
);
```
Les possibilités pour `$conditions` sont :
* `EntityRepository::OR_WHERE` : Pour des (x OR y OR ...)
* `EntityRepository::AND_WHERE` : Pour des (x AND y AND ...)
* `EntityRepository::WHERE_LIKE` : Pour des (x LIKE y)
* `EntityRepository::WHERE_IN` : Pour des (x IN (y1, y2...))
* `EntityRepository::WHERE_NULL` : Pour des (x IS NULL)
* `EntityRepository::WHERE_NOT_NULL` : Pour des (x IS NOT NULL)
* `EntityRepository::WHERE_EQUAL` : Pour des x = y
* `EntityRepository::WHERE_LESS` : Pour des x < y
* `EntityRepository::WHERE_LESS_OR_EQUAL` : Pour des x <= y
* `EntityRepository::WHERE_GREATER` : Pour des x > y
* `EntityRepository::WHERE_GREATER_OR_EQUAL` : Pour des x >= y


Exemple d'utilisation de `$extras` pour le manager (fictif) `VilleManager` :
```php
$extras = array
(
    EntityRepository::INNER_JOINS => array
    (
        'ville.maison' => 'maison'
    ),
    EntityRepository::SELECTS => array
    (
        'maison' => EntityRepository::SELECT_JOIN
    )
);
```
Les possibilités pour `$extras` sont :
* `EntityRepository::SELECTS` : Met à jour l'entité avec une jointure avec EntityRepository::SELECT_JOIN comme valeur (cf. Exemple ci-dessus) ou sinon ajoute une valeur à remonter.
* `EntityRepository::LEFT_JOINS`
* `EntityRepository::INNER_JOINS`
* `EntityRepository::GROUP_BYS`


