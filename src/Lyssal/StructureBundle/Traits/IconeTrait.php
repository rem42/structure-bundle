<?php
namespace Lyssal\StructureBundle\Traits;

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
     * Répertoire dans lequel est enregistré l'icône
     * 
     * @return string Dossier de l'icône
     */
    abstract public function getIconeUploadDir();
    
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
     * Retourne si l'entité possède l'icône.
     * 
     * @return boolean VRAI si icône existant
     */
    public function hasIcone()
    {
        return (null !== $this->icone);
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
     * @deprecated Use getIconePathname
     * @return string Chemin de l'icône
     */
    public function getIconeChemin()
    {
        return $this->getIconePathname();
    }
    
    /**
     * Retourne le chemin (pathname) de l'icône.
     *
     * @return string Chemin de l'icône
     */
    public function getIconePathname()
    {
        return $this->getIconeUploadDir().DIRECTORY_SEPARATOR.$this->icone;
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
        $this->deleteIcone();

        $fichier = new Fichier($this->iconeFile->getRealPath());
        $fichier->move($this->getIconeUploadDir().DIRECTORY_SEPARATOR.$this->iconeFile->getClientOriginalName(), $remplaceSiExistant);
        $this->icone = $fichier->getNom();
        $this->setIconeFile(null);
    }
    
    /**
     * Supprime le fichier.
     */
    public function deleteIcone()
    {
        if ('' != $this->icone && file_exists($this->getIconeChemin()))
            unlink($this->getIconeChemin());
    }
}
