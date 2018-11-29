<?php
    function chargerClasse($classname)
    {
        require $classname. ".php";
    }
    spl_autoload_register('chargerClasse');

    // Démarre la session
    session_start();

    // Déconnexion et destruction de la session
    if(isset($_GET['deconnexion']))
    {
        session_destroy();
        header('Location: .');
        exit();
    }

    //Si la session existe, on la récupère
    if(isset($_SESSION['perso']))
    {
        $perso = $_SESSION['perso'];
    }

    // Creation de l'objet PDO
    $db = new PDO('mysql:host=localhost;dbname=poo', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); // On émet une alerte à chaque fois qu'une requête a échoué.

    $manager = new PersonnagesManager($db);

    // Créer un personnage
    if(isset($_POST['creer']) && isset($_POST['nom'])){

        $perso = new Personnage(['nom' => $_POST['nom']]);

        if(!$perso->nomValide())
        {
            $message = "<strong>Le nom choisi est invalide !</strong>";
            unset($perso);
        }
        elseif ($manager->exists($perso->nom()))
        {
            $message = "<strong>Le nom choisi est déjà pris !</strong>";
            unset($perso);
        }
        else
        {
            $manager->add($perso);
        }
    }
    // Utiliser le personnage
    elseif (isset($_POST['utiliser']) && isset($_POST['nom']))
    {
        if($manager->exists($_POST['nom']))
        {
            $perso = $manager->get($_POST['nom']);
        }
        else
        {
            $message = "<strong>Ce personnage n'existe pas !</strong>";
        }
    }
    // Si on a cliqué sur un personnage pour le frapper
    elseif (isset($_GET['frapper']))
    {
        if(!isset($perso))
        {
            $message = '<strong>Merci de créer un personnage ou de vous identifier.</strong>';
        }
        else
        {
            if(!$manager->exists((int) $_GET['frapper']))
            {
                $message = "<strong>Le personnage que vous voulez frapper n'existe pas !</strong>";
            }
            else
            {
                $persoAfrapper = $manager->get((int) $_GET['frapper']);
                // On stocke dans $retour les éventuelles erreurs ou messages que renvoie la méthode frapper.
                $retour = $perso->frapper($persoAfrapper);

                switch ($retour)
                {
                    case PERSONNAGE::CEST_MOI :
                        $message = "<strong>Mais Pourquoi voulez-vous vous frapper !?</strong> ";
                        break;

                    case PERSONNAGE::PERSONNAGE_FRAPPE :
                        $message = "<strong>Le personnage a bien été frappé !</strong>";

                        $manager->update($perso);
                        $manager->update($persoAfrapper);
                        break;

                    case PERSONNAGE::PERSONNAGE_TUE :
                        $message = "<strong>Le personnage a été tué !!</strong>";

                        $manager->update($perso);
                        $manager->delete($persoAfrapper);
                        break;
                }
            }
        }
    }
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mini-jeu</title>
</head>
<body>
    <p>Nombre de personnages créés : <?= $manager->count(); ?></p>
    <?php
        if(isset($message))
        {
            echo "<p>" .$message. "</p>";
        }

        // Personnage créé ou utilisé
        if(isset($perso))
        {
            ?>

                <p><a href="?deconnexion=1">Déconnexion</a> </p>
                <fieldset>
                    <legend>Mes informations</legend>
                    <p>
                        Nom : <?= htmlspecialchars($perso->nom()); ?> <br/>
                        Dégats : <?= $perso->degats(); ?> <br/>
                        Force : <?= $perso->forcePerso(); ?> <br/>
                        Experience : <?= $perso->experience(); ?> <br/>
                        Niveau : <?= $perso->niveau(); ?>
                    </p>
                </fieldset>
                <fieldset>
                    <legend>Qui frapper ?</legend>
                    <p>

                        <?php
                            $persos = $manager->getList($perso->nom());
                            if(empty($persos))
                            {
                                echo 'Personne à frapper !';
                            }
                            else
                            {
                                foreach ($persos as $unPerso)
                                {
                                    echo '<a href="?frapper=' .$unPerso->id(). '">' .htmlspecialchars($unPerso->nom()). '</a> (dégâts : ' .$unPerso->degats(). ')<br/>';
                                }
                            }
                        ?>
                    </p>
                </fieldset>
            <?php
        }
        else {

            ?>

            <form method="POST" action="">
                <label for="nom">Nom : </label>
                <input type="text" name="nom" maxlength="50">
                <input type="submit" value="Créer ce personnage" name="creer"/>
                <input type="submit" value="Utiliser ce personnage" name="utiliser"/>
            </form>
            <?php
        }
            ?>
</body>
</html>
<?php

    if(isset($perso))
    {
        $_SESSION['perso'] = $perso;
    }