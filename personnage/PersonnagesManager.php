<?php

    class PersonnagesManager
    {
        private $_db; // Instance de PDO.

        public function __construct($db)
        {
            $this->setDb($db);
        }

        public function setDb(PDO $db)
        {
            $this->_db = $db;
        }

        public function add(Personnage $perso)
        {
            // Préparation de la requête d'insertion.
            // Assignation des valeurs pour le nom, la force, les dégâts, l'expérience et le niveau du personnage.
            // Exécution de la requête.
            $q = $this->_db->prepare('INSERT INTO personnages(nom, forcePerso, degats, niveau, experience) VALUES(:nom, :forcePerso, :degats, :niveau, :experience)');
            $q->bindValue(':nom', $perso->nom());
            $q->bindValue(':forcePerso', $perso->forcePerso(), PDO::PARAM_INT);
            $q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
            $q->bindValue(':niveau', $perso->niveau(), PDO::PARAM_INT);
            $q->bindValue(':experience', $perso->experience(), PDO::PARAM_INT);

            $q->execute();
        }

        public function delete(Personnage $perso)
        {
            $this->_db->exec('DELETE FROM personnages WHERE id =' .$perso->id());
        }

        public function get($id)
        {
            // Exécute une requête de type SELECT avec une clause WHERE, et retourne un objet Personnage.
            $id = (int) $id;
            $q = $this->_db->query('SELECT id, nom, forcePerso, degats, niveau, experience FROM personnages WHERE id = '. $id);
            $donnees = $q->fetch(PDO::FETCH_ASSOC);
            return new Personnage($donnees);
        }

        public function getList()
        {
            // Retourne la liste de tous les personnages.
            $persos = [];
            $q = $this->_db->query('SELECT id, nom, forcePerso, degats, niveau, experience FROM personnages ORDER BY nom');

            while($donnees = $q->fetch(PDO::FETCH_ASSOC))
            {
                $persos[] = new Personnage($donnees);
            }
            return $persos;
        }

        public function update(Personnage $perso)
        {
            // Prépare une requête de type UPDATE.
            // Assignation des valeurs à la requête.
            // Exécution de la requête.
            $q = $this->_db->prepare('UPDATE personnages SET nom = :nom, forcePerso = :forcePerso, degats = :degats, niveau = :niveau, experience = :experience WHERE id = :id');
            $q->bindValue(':nom', $perso->nom());
            $q->bindValue(':forcePerso', $perso->forcePerso(), PDO::PARAM_INT);
            $q->bindValue(':degats', $perso->degats(), PDO::PARAM_INT);
            $q->bindValue(':niveau', $perso->niveau(), PDO::PARAM_INT);
            $q->bindValue(':experience', $perso->experience(), PDO::PARAM_INT);
            $q->bindValue(':id', $perso->id(), PDO::PARAM_INT);

            $q->execute();
        }

    }