<?php

    class PersonnagesManager
    {
        private $_db;

        public function __construct($db)
        {
            $this->setDb($db);
        }

        public function setDb(PDO $db){
            $this->_db = $db;
        }

        //Enregistrer un personnage
        public function add(Personnage $perso){
            $q = $this->_db->prepare("INSERT INTO perso(nom) VALUES(:nom)");
            $q->bindValue(":nom", $perso->nom());
            $q->execute();

            $perso->hydrate([
                "id" => $this->_db->lastInsertId(),
                "degats" => 0,
                "experience" => 0,
                "niveau" => 1,
                "forcePerso" => 0
            ]);
        }

        //Modifier un personnage
        public function update(Personnage $perso){
            $q = $this->_db->prepare("UPDATE perso SET degats = :degats, experience = :experience, niveau = :niveau, forcePerso = :forcePerso WHERE id = :id");
            $q->bindValue(":degats", $perso->degats(), PDO::PARAM_INT);
            $q->bindValue(":experience", $perso->experience(), PDO::PARAM_INT);
            $q->bindValue(":niveau", $perso->niveau(), PDO::PARAM_INT);
            $q->bindValue(":forcePerso", $perso->forcePerso(), PDO::PARAM_INT);
            $q->bindValue(":id", $perso->id(), PDO::PARAM_INT);

            $q->execute();
        }

        //Sélectionner un personnage
        public function get($info){

            if(is_int($info)) {
                $q = $this->_db->query("SELECT id, nom, degats, experience, niveau, forcePerso FROM perso WHERE id = " .$info);
                $donnees = $q->fetch(PDO::FETCH_ASSOC);
                return new Personnage($donnees);
            } else {
                $q = $this->_db->prepare("SELECT id, nom, degats, experience, niveau, forcePerso FROM perso WHERE nom = :nom");
                $q->bindValue(":nom", $info);
                $q->execute();
                return new Personnage($q->fetch(PDO::FETCH_ASSOC));
            }
        }

        //Supprimer un personnage
        public function delete(Personnage $perso){
            $this->_db->query("DELETE FROM perso WHERE id = ". $perso->id());
        }

        //Compter le nombre de personnages
        public function count(){
            return $this->_db->query("SELECT COUNT(*) FROM perso")->fetchColumn();
        }

        //Vérifie si un personnage existe
        public function exists($info){
            if(is_int($info)){
                return (bool) $this->_db->query("SELECT COUNT(*) FROM perso WHERE id = " .$info)->fetchColumn();
            }

            $q = $this->_db->prepare('SELECT COUNT(*) FROM perso WHERE nom = :nom');
            $q->bindValue(':nom', $info);
            $q->execute();
            return (bool) $q->fetchColumn();
        }

        //Selectionne tous les personnages differents de $nom
        public function getList($nom)
        {
            // Retourne la liste des personnages dont le nom n'est pas $nom.
            // Le résultat sera un tableau d'instances de Personnage.
            $persos = [];
            $q = $this->_db->prepare("SELECT id, nom, degats, experience, niveau, forcePerso FROM perso WHERE nom <> :nom ORDER BY nom");
            $q->bindValue(':nom', $nom);
            $q->execute();

            while ($donnees = $q->fetch(PDO::FETCH_ASSOC)){
                $persos[] = new Personnage($donnees);
            }
            return $persos;
        }
    }