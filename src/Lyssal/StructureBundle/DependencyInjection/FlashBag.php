<?php
namespace Lyssal\StructureBundle\DependencyInjection;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashBag
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionBagInterface FlashBag
     */
    private $sessionFlashBag;


    /**
     * Constructeur.
     * 
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface        $session  Session
     * @param \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag FlashBag
     */
    public function __construct(SessionInterface $session, FlashBagInterface $flashBag = null)
    {
        $this->sessionFlashBag = $session->getBag($flashBag->getName());
    }


    /**
     * Retourne le FlashBag.
     * 
     * @return \Symfony\Component\HttpFoundation\Session\SessionBagInterface FlashBag
     */
    public function getSessionFlashBag()
    {
        return $this->sessionFlashBag;
    }

    /**
     * Crée un message Flash.
     * 
     * @param string $type    Type de message
     * @param string $message Texte du message
     */
    public function addFlash($type, $message)
    {
        if ($this->sessionFlashBag instanceof \Symfony\Component\HttpFoundation\Session\Flash\FlashBag)
            $this->sessionFlashBag->add($type, $message);
        else throw new \Exception('FlashBag inconnu, veuillez hériter cette méthode.');
    }
}
