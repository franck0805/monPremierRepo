<?php
class Magicien extends Personnage
{
    private $_magie;

    public function lancerUnSort($perso)
    {
        $perso->recevoirDegats($this->_magie);
    }

    public function gagnerExperience()
    {
        // On appelle la méthode gagnerExperience() de la classe parente
        parent::gagnerExperience();

        if($this->_magie < 100)
        {
            $this->_magie += 10;
        }
    }

    public function magie() { return $this->_magie; }
    public function setMagie($magie)
    {
        $magie = (int) $magie;
        if($magie >= 0 && $magie <= 100)
        {
            $this->_magie = $magie;
        }
    }

}