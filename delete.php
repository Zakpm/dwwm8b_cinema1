<?php
session_start();

//  Si aucun identifiant de film n'a été envoyé via la méthode GET 
if( !isset($_GET['film_id']) || empty($_GET['film_id'])) {

    //  Effectue une redirection vers la page d'accueil 
    //  Arrêter l'exécution du script
            return header("Location: index.php");
        }
    
    //  Dans le cas contraire, 
    
    // Récuperer l'identifiant du film de $_GET en pretegeant le serveur contre les faille de type XSS
    $film_id = strip_tags($_GET['film_id']);
    
    
    // Convertir l'identifiant du film pour être sûr de travailler avec un entier
    $film_id_coverted = (int) $film_id;
    // $film_id_coverted = intval($film_id);
    
    
    // Etablir une connexion avec la base de données
    require __DIR__ . "/db/connexion.php";
    
    
    // Effectuer la requête de selection afin de vérifier si l'identifiant du film correspond à celui de la tablbe "film"
    $req = $db -> prepare("SELECT * FROM film WHERE id = :id");
    $req -> bindValue(":id", $film_id_coverted);
    $req -> execute();
    $count = $req -> rowCount();
    
    
    // Si le nombre total d'enregistrement récupéré n'est pas égal à 1,
    // Rediriger l'utilisateur vers la page d'accueil 
    // Arrêter automatiquement l'exécution du script
    if ($count != 1) {
    
        return header("Location: index.php");
    
    }
    
    // Dans le cas contraire
    // Récuréper le film en question
    $film = $req -> fetch();
    
    // Fermer le curseur
    $req -> closeCursor();


    // Effectuer une seconde requête pour supprimer le film
    $delate_req = $db -> prepare("DELETE FROM film WHERE id = :id");
    $delate_req -> bindValue(":id", $film['id']);
    $delate_req -> execute();
    $delate_req -> closeCursor();


    // Générer le message flash 
    $_SESSION["success"] = $film['name'] . "a été retiré de la liste.";


    // Effectuer la redirection vers la page d'accueil et arrêter l'exécution du scipt 
     return header("Location: index.php");
?>