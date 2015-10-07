<?php
namespace Lyssal\StructureBundle\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Lyssal\Fichier;

/**
 * Trait pour les entités ayant un fichier.
 * 
 * Il faut définir dans la class parente la propriété "$fichier" ainsi que la méthode "getFichierUploadDir()" qui renvoie le chemin du dossier où sont situés les fichiers.
 * 
 * @author Rémi Leclerc
 */
trait FichierTrait
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    protected $fichierFile;
    
    /**
     * Répertoire dans lequel est enregistré le fichier
     * 
     * @return string Dossier du fichier
     */
    abstract public function getFichierUploadDir();
    
    /**
     * Get Fichier
     * 
     * @return string Fichier
     */
    public function getFichier()
    {
        return $this->fichier;
    }
    /**
     * Set Fichier
     * 
     * @param string $fichier
     * @return \Lyssal\StructureBundle\Entity\FichierTrait
     */
    public function setFichier($fichier)
    {
        $this->fichier = $fichier;
        return $this;
    }
    
    /**
     * Retourne si l'entité possède le fichier.
     * 
     * @return boolean VRAI si fichier existant
     */
    public function hasFichier()
    {
        return (null !== $this->fichier);
    }
    
    /**
     * Get FichierFile
     * 
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile FichierFile
     */
    public function getFichierFile()
    {
        return $this->fichierFile;
    }
    
    /**
     * Set FichierFile
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $fichierFile
     * @return \Lyssal\StructureBundle\Entity\FichierTrait
     */
    public function setFichierFile(UploadedFile $fichierFile = null)
    {
        $this->fichierFile = $fichierFile;
        if (null !== $this->fichierFile)
            $this->uploadFichier();
        return $this;
    }
    
    /**
     * Retourne le chemin de le fichier.
     *
     * @return string Chemin de le fichier
     */
    public function getFichierChemin()
    {
        return $this->getFichierUploadDir().DIRECTORY_SEPARATOR.$this->fichier;
    }
    /**
     * Enregistre le fichier sur le disque.
     * 
     * @return void
     */
    protected function uploadFichier()
    {
        $this->saveFichier(false);
    }
    /**
     * Enregistre le fichier sur le disque.
     * 
     * @return void
     */
    protected function saveFichier($remplaceSiExistant = false)
    {
        $this->deleteFichier();

        $fichier = new Fichier($this->fichierFile->getRealPath());
        $fichier->move($this->getFichierUploadDir().DIRECTORY_SEPARATOR.$this->fichierFile->getClientOriginalName(), $remplaceSiExistant);
        $this->fichier = $fichier->getNom();
        $this->setFichierFile(null);
    }
    
    /**
     * Supprime le fichier.
     */
    public function deleteFichier()
    {
        if ('' != $this->fichier && file_exists($this->getFichierChemin()))
            unlink($this->getFichierChemin());
    }
}
