# Traits

## IconeTrait

IconeTrait permet de gérer une icône dans votre entité. Le trait gère l'enregistrement, le remplacement et la suppression automatiquement.

### Utilisation

1. Appelez le trait dans votre entité :
```php
use Lyssal\StructureBundle\Traits\IconeTrait;
```
2. Créez les variables `icone` et `iconeFile` ainsi :
```php
/**
 * @var string
 * 
 * @ORM\Column(name="icone", type="string", length=255, nullable=false)
 */
private $icone;

/**
 * @var \Symfony\Component\HttpFoundation\File\File
 */
private $iconeFile;
```
3. Définir la méthode `getIconeUploadDir()` :
```php
/**
 * Répertoire dans lequel est enregistré l'icône.
 * 
 * @return string Dossier de l'icône
 */
protected function getIconeUploadDir()
{
    return 'img'.DIRECTORY_SEPARATOR.'icones';
}
private $icone;
```

Vous pouvez ensuite utiliser la propriété `iconeFile` dans vos formulaires.

De même avec `SonataAdmin` :
```php
protected function configureFormFields(FormMapper $formMapper)
{
    $formMapper
        //...
        ->add
        (
            'iconeFile',
            'file',
            array
            (
                // Si l'icône est obligatoire, on ne le demande qu'à la création de l'entité
                'required' => (null === $this->getSubject()->getId())
            )
        )
    ;
}
```

Le chemin de l'icône se récupère avec la méthode `getIconePathname()` :
```php
echo $icone->getIconePathname();
```

Vous pouvez également redéfinir la méthode `iconeFileIsValid()` pour définir si l'icône chargée par l'utilisateur est valide ou pas (par exemple en fonction du poids du fichier). Si elle n'est pas valide, elle ne sera pas enregistrée sur le serveur.
Vous pouvez savoir si l'icône a été enregistrée ou non avec la méthode `iconeFileHasBeenUploaded()`.


### Utilisation avancée

IconeTrait permet également de manipuler très simplement les images par le biais de la librairie PHP `Lyssal` et de dupliquer l'icône (pour l'enregistrer sous différentes dimensions ou minifier le nom du fichier enregistré par exemple).
Pour se faire, il suffit de surcharger la méthode `uploadIcone()`.

Dans cet exemple, nous enregistrons notre image en double en 32x32 px et 16x16 px :

```php
use Doctrine\ORM\Mapping as ORM;
use Lyssal\StructureBundle\Traits\IconeTrait;

/**
 * Mon entité.
 * 
 * @ORM\Entity()
 * @ORM\Table(name="lyssaltourisme_structure_type")
 */
class Entite
{
    use IconeTrait;
    
    // ...
    
    /**
     * @var string
     * 
     * @ORM\Column(name="icone", type="string", length=64, nullable=false)
     */
    private $icone;

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     *
     * @Assert\Image(
     *     maxSize="200Ki",
     *     mimeTypes = {"image/png", "image/jpeg", "image/gif"},
     *     mimeTypesMessage = "Veuillez choisir une image PNG, JPEG ou GIF."
     * )
     */
    private $iconeFile;

    /**
     * Répertoire dans lequel est enregistré l'icône.
     * 
     * @return string Dossier de l'icône
     */
    protected function getIconeUploadDir()
    {
        return 'img'.DIRECTORY_SEPARATOR.'icones'.DIRECTORY_SEPARATOR.'32';
    }
    /**
     * Retourne l'URL de l'icône 32px.
     * 
     * @return string URL de l'icône 32px
     */
    public function getIcone32Url()
    {
        return $this->getIconePathname();
    }
    /**
     * Retourne l'URL de l'icône 16px.
     * 
     * @return string URL de l'icône 16px
     */
    public function getIcone16Url()
    {
        return 'img'.DIRECTORY_SEPARATOR.'icones'.DIRECTORY_SEPARATOR.'16'.DIRECTORY_SEPARATOR.$this->icone;
    }
    /**
     * Enregistre l'icône sur le disque.
     *
     * @return void
     */
    protected function uploadIcone()
    {
        // Si notre ancien icône 16px existe, on le supprime
        if (null !== $this->icone && file_exists($this->getIcone16Url()))
            unlink($this->getIcone16Url());
            
        // On enregistre la nouvelle icône (la méthode supprime l'éventuelle ancienne icône)
        $this->saveIcone(false);
        
        // On minifie le nom du fichier avec le nom de l'entité
        $icone = new Image($this->getIconePathname());
        $icone->setNomMinifie($this->nom, '-', true, 64);
        $this->icone = $icone->getNom();
        
        // On copie l'icône pour le 16px
        $icone16 = $icone->copy($this->getIcone16Url(), false);

        // On redimensionne correctement nos icônes
        $icone->redimensionne(32, 32);
        $icone16->redimensionne(16, 16);
    }
}
```

### Méthodes publiques

* `getIconeUploadDir()` : Retourne le répertoire dans lequel est enregistré l'icône
* `getIcone()` : Retourne l'icône
* `setIcone($icone)` : Spécifie l'icône
* `hasIcone()` :Retourne si l'entité possède une icône
* `getIconeFile()` : Retourne le fichier chargé par l'utilisateur
* `setIconeFile(UploadedFile $iconeFile)` : Spécifie le fichier chargé par l'utilisateur
* `iconeFileIsValid()` : Retourne si l'icône chargée par l'utilisateur est valide
* `iconeFileHasBeenUploaded()` : Retourne si l'icône a été chargée (enregistrée)
* `getIconePathname()` : Retourne le chemin (pathname) de l'icône
* `deleteIcone()` : Supprime le fichier du serveur


## ImageTrait

`ImageTrait` permet de gérer une image dans votre entité (par exemple l'avatar d'une entité `Utilisateur`). Son utilisation est strictement identique à `IconeTrait` (il faut juste remplacer (I|i)cone par (I|i)mage à chaque fois).


## FichierTrait

`FichierTrait` permet de gérer un fichier en général dans votre entité. Son utilisation est strictement identique à `IconeTrait` (il faut juste remplacer (I|i)cone par (F|f)ichier à chaque fois).
