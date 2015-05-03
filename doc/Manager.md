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
Exemple de `$extras` pour le manager (fictif) `VilleManager` :
```php
$extras = array
(
    'innerJoins' => array
    (
        'ville.maison' => 'maison'
    ),
    'likes' => array
    (
        'maison.adresse' => '% rue %'
    )
);
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
