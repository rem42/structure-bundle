# Traits

## IconeTrait

IconeTrait permet de gérer une icône dans votre entité.

### Utilisation

1. Appelez le trait dans votre entité :
```php
use Lyssal\StructureBundle\Traits\IconeTrait;
```
2. Créez la propriété `icone` :
```php
/**
 * @var string
 * 
 * @ORM\Column(name="icone", type="string", length=255, nullable=false)
 */
private $icone;
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
    return 'img/icones';
}
private $icone;
```

Vous devez ensuite utiliser la propriété `iconeFile` (définie dans le trait) dans vos formulaires.
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

Le chemin de l'icône se récupère avec la méthode `getIconeChemin()` :
```php
echo $icone->getIconeChemin();
```

getIconeChemin

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
     * Répertoire dans lequel est enregistré l'icône.
     * 
     * @return string Dossier de l'icône
     */
    protected function getIconeUploadDir()
    {
        return 'img/icones/32';
    }
    /**
     * Retourne l'URL de l'icône 32px.
     * 
     * @return string URL de l'icône 32px
     */
    public function getIcone32Url()
    {
        return $this->getIconeChemin();
    }
    /**
     * Retourne l'URL de l'icône 16px.
     * 
     * @return string URL de l'icône 16px
     */
    public function getIcone16Url()
    {
        return 'img/icones/16/'.$this->icone;
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
        $icone = new Image($this->getIconeChemin());
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

## ImageTrait

`ImageTrait` permet de gérer une image dans votre entité (par exemple l'avatar d'une entité `Utilisateur`). Son utilisation est strictement identique à `IconeTrait` (il faut juste remplacer (I|i)cone par (I|i)mage à chaque fois).

## FichierTrait

`FichierTrait` permet de gérer un fichier en général dans votre entité. Son utilisation est strictement identique à `IconeTrait` (il faut juste remplacer (I|i)cone par (F|f)ichier à chaque fois).
