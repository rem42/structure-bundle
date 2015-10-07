<?php
namespace Lyssal\StructureBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Lyssal\Fichier;

/**
 * Trait pour les entités ayant une image.
 * 
 * Il faut définir dans la class parente la propriété "$image" ainsi que la méthode "getImageUploadDir()" qui renvoie le chemin du dossier où sont situés les images
 * 
 * @author Rémi Leclerc
 */
trait ImageTrait
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     * 
     * @Assert\Image(
     *     mimeTypes = {"image/png", "image/jpeg", "image/gif"},
     *     mimeTypesMessage = "Veuillez choisir une image PNG, JPEG ou GIF."
     * )
     */
    protected $imageFile;
    
    /**
     * Répertoire dans lequel est enregistré l'image
     * 
     * @return string Dossier de l'image
     */
    abstract public function getImageUploadDir();
    
    /**
     * Get Image
     * 
     * @return string Image
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Set Image
     * 
     * @param string $image
     * @return \Lyssal\StructureBundle\Entity\ImageTrait
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Retourne si l'entité possède l'image.
     * 
     * @return boolean VRAI si image existant
     */
    public function hasImage()
    {
        return (null !== $this->image);
    }
    
    /**
     * Get ImageFile
     * 
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile ImageFile
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set ImageFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     * @return \Lyssal\StructureBundle\Entity\ImageTrait
     */
    public function setImageFile(UploadedFile $imageFile = null)
    {
        $this->imageFile = $imageFile;
        if (null !== $this->imageFile)
            $this->uploadImage();
        return $this;
    }
    
    /**
     * Retourne le chemin de l'image.
     *
     * @return string Chemin de l'image
     */
    public function getImageChemin()
    {
        return $this->getImageUploadDir().DIRECTORY_SEPARATOR.$this->image;
    }
    /**
     * Enregistre l'image sur le disque.
     * 
     * @return void
     */
    protected function uploadImage()
    {
        $this->saveImage(false);
    }
    /**
     * Enregistre l'image sur le disque.
     * 
     * @return void
     */
    protected function saveImage($remplaceSiExistant = false)
    {
        $this->deleteImage();

        $fichier = new Fichier($this->imageFile->getRealPath());
        $fichier->move($this->getImageUploadDir().DIRECTORY_SEPARATOR.$this->imageFile->getClientOriginalName(), $remplaceSiExistant);
        $this->image = $fichier->getNom();
        $this->setImageFile(null);
    }
    
    /**
     * Supprime le fichier.
     */
    public function deleteImage()
    {
        if ('' != $this->image && file_exists($this->getImageChemin()))
            unlink($this->getImageChemin());
    }
}
