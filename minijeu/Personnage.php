<?php
class Personnage
{
    private $_id;
    private $_nom;
    private $_degats;
    private $_experience;
    private $_niveau;
    private $_forcePerso;

    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;

    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    public function hydrate($donnees)
    {
        foreach ($donnees as $key => $value){

            $method = 'set'.ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }

    public function frapper(Personnage $perso)
    {
        // Avant tout : vérifier qu'on ne se frappe pas soi-même.
        // Si c'est le cas, on stoppe tout en renvoyant une valeur signifiant que le personnage ciblé est le personnage qui attaque.
        // On indique au personnage frappé qu'il doit recevoir des dégâts.
        if($this->id() == $perso->id()){
            return self::CEST_MOI;
        }

        $this->gagnerExperience();
        return $perso->recevoirDegats($this->_forcePerso);
    }

    public function recevoirDegats($force)
    {
        // On augmente de 5 les dégâts + la force du personnage qi frappe
        // Si on a 100 de dégâts ou plus, la méthode renverra une valeur signifiant que le personnage a été tué.
        // Sinon, elle renverra une valeur signifiant que le personnage a bien été frappé.


        // On augmente de 5 les degats reçus
        $this->_degats = $this->_degats + $force + 5;

        if($this->_degats >= 100){
            // Le personnage est mort
            return self::PERSONNAGE_TUE;
        }
        // Le personnage a été frappé
        return self::PERSONNAGE_FRAPPE;
    }

    // Gagner de l'experience au personnage
    public function gagnerExperience()
    {
        $this->_experience += 1;
        if($this->_experience > 100)
        {
            $this->_experience = 0;
            $this->_niveau += 1;
            $this->_forcePerso += 10;
        }
    }

    public function id() { return $this->_id; }
    public function nom() { return $this->_nom; }
    public function degats() { return $this->_degats; }
    public function experience() { return $this->_experience; }
    public function niveau() { return $this->_niveau; }
    public function forcePerso() { return $this->_forcePerso; }

    public function setId($id)
    {
        $id = (int) $id;

        if($id > 0) {
            $this->_id = (int)$id;
        }
    }

    public function setNom($nom)
    {
        if(is_string($nom) && strlen($nom) <= 30){
            $this->_nom = $nom;
        }
    }

    public function setDegats($degats)
    {
        $degats = (int) $degats;
        if($degats >= 0 && $degats <= 100){
            $this->_degats = $degats;
        }
    }

    public function setExperience($experience)
    {
        $experience = (int) $experience;
        if($experience >= 0 && $experience <= 100)
        {
            $this->_experience = $experience;
        }
    }

    public function setNiveau($niveau)
    {
        $niveau = (int) $niveau;
        if($niveau >= 1 && $niveau <= 100)
        {
            $this->_niveau = $niveau;
        }
    }

    public function setForcePerso($forcePerso)
    {
        $forcePerso = (int) $forcePerso;
        if($forcePerso >= 0)
        {
            $this->_forcePerso = $forcePerso;
        }
    }

    // Teste si le nom du personnage n'est pas vide
    public function nomValide()
    {
        return !empty($this->_nom);
    }



}