<?php
namespace Lyssal\StructureBundle\Traits;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Lyssal\Fichier;

/**
 * Trait pour les entités ayant un fichier.
 *
 * Il faut définir dans la classe qui utilise ce trait les propriétés $fichier et $fichierFile ainsi que la méthode getFichierUploadDir() qui renvoie le chemin du dossier où sont situées les fichiers.
 *
 * @author Rémi Leclerc
 */
trait FichierTrait
{
    /**
     * @var boolean Si le fichier a été chargé
     */
    protected $fichierFileHasBeenUploaded = false;


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
     * Retourne si l'entité possède un fichier.
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

        if (null !== $this->fichierFile && $this->fichierFileIsValid()) {
            $this->uploadFichier();
        }

        return $this;
    }

    /**
     * Retourne si le fichier chargé par l'utilisateur est valide.
     *
     * @return boolean Si valide
     */
    public function fichierFileIsValid()
    {
        return (null !== $this->fichierFile);
    }

    /**
     * Retourne si le fichier a été chargé (enregistré sur le serveur).
     *
     * @return boolean Si chargé
     */
    public function fichierFileHasBeenUploaded()
    {
        return $this->fichierFileHasBeenUploaded;
    }
    
    /**
     * Retourne le chemin de le fichier.
     *
     * @deprecated Use getFichierPathname
     * @return string Chemin de le fichier
     */
    public function getFichierChemin()
    {
        return $this->getFichierPathname();
    }
    
    /**
     * Retourne le chemin (pathname) de le fichier.
     *
     * @return string Chemin de le fichier
     */
    public function getFichierPathname()
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
        if ($fichier->move($this->getFichierUploadDir().DIRECTORY_SEPARATOR.$this->fichierFile->getClientOriginalName(), $remplaceSiExistant)) {
            $this->fichier = $fichier->getNom();
            $this->setFichierFile(null);
            $this->fichierFileHasBeenUploaded = true;
        }
    }
    
    /**
     * Supprime le fichier du serveur.
     */
    public function deleteFichier()
    {
        if ('' != $this->fichier && file_exists($this->getFichierChemin()))
            unlink($this->getFichierChemin());
    }
}
