<?php
namespace Lyssal\StructureBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Lyssal\Fichier;

/**
 * Trait pour les entités ayant une icône.
 * 
 * Il faut définir dans la class parente la propriété "$icone" ainsi que la méthode "getIconeUploadDir()" qui renvoie le chemin du dossier où sont situés les icônes
 * 
 * @author Rémi Leclerc
 */
trait IconeTrait
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     * 
     * @Assert\Image(
     *     mimeTypes = {"image/png", "image/jpeg", "image/gif"},
     *     mimeTypesMessage = "Veuillez choisir une image PNG, JPEG ou GIF."
     * )
     */
    protected $iconeFile;
    
    /**
     * Répertoire dans lequel est enregistré l'icône
     * 
     * @return string Dossier de l'icône
     */
    abstract protected function getIconeUploadDir();
    
    /**
     * Get Icone
     * 
     * @return string Icone
     */
    public function getIcone()
    {
        return $this->icone;
    }
    /**
     * Set Icone
     * 
     * @param string $icone
     * @return \Lyssal\StructureBundle\Entity\IconeTrait
     */
    public function setIcone($icone)
    {
        $this->icone = $icone;
        return $this;
    }
    
    /**
     * Get IconeFile
     * 
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile IconeFile
     */
    public function getIconeFile()
    {
        return $this->iconeFile;
    }
    /**
     * Set IconeFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $iconeFile
     * @return \Lyssal\StructureBundle\Entity\IconeTrait
     */
    public function setIconeFile(UploadedFile $iconeFile = null)
    {
        $this->iconeFile = $iconeFile;
        if (null !== $this->iconeFile)
            $this->uploadIcone();
        return $this;
    }
    
    /**
     * Retourne le chemin de l'icône.
     *
     * @return string Chemin de l'icône
     */
    public function getIconeChemin()
    {
        return $this->getIconeUploadDir().'/'.$this->icone;
    }
    /**
     * Enregistre l'icône sur le disque.
     * 
     * @return void
     */
    protected function uploadIcone()
    {
        $this->saveIcone(false);
    }
    /**
     * Enregistre l'icône sur le disque.
     * 
     * @return void
     */
    protected function saveIcone($remplaceSiExistant = false)
    {
        if (null !== $this->icone && file_exists($this->getIconeChemin()))
            unlink($this->getIconeChemin());

        $fichier = new Fichier($this->iconeFile->getRealPath());
        $fichier->move($this->getIconeUploadDir().DIRECTORY_SEPARATOR.$this->iconeFile->getClientOriginalName(), $remplaceSiExistant);
        $this->icone = $fichier->getNom();
        $this->setIconeFile(null);
    }
}
