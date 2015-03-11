# LyssalStructureBundle

LyssalStructureBundle contient différents outils facilitant le développement d'applications Symfony.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6e0264ed-220c-4726-92b5-a174e0556cf0/small.png)](https://insight.sensiolabs.com/projects/6e0264ed-220c-4726-92b5-a174e0556cf0)

## Documentation

* [Manager de base](doc/Manager.md)
* [Repository de base](doc/Repository.md)
* [Decorator](doc/Decorator.md)
* [Appellation](doc/Appellation.md)
* [Traits pour entité](doc/Traits.md)

### Autres bundles

* [Thèmes Pagefanta](doc/Pagerfanta.md)
* [Vues Simplepie](doc/Simplepie.md)
* [Vues DebrilRssAtomBundle](doc/DebrilRssAtomBundle.md)

## Installation

1. Mettez à jour votre `composer.json` :
```json
"require": {
    "lyssal/structure-bundle": "*"
}
```
2. Installez le bundle :
```sh
php composer.phar update
```
3. Mettez à jour `AppKernel.php` :
```php
new Lyssal\StructureBundle\LyssalStructureBundle(),
```
