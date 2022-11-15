<?php 
session_start();

//  Si aucun identifiant de film n'a été envoyé via la méthode GET 
    if( !isset($_GET['film_id']) || empty($_GET['film_id'])) {
        

//  Effectue une redirection vers la page d'accueil 
//  Arrêter l'exécution du script
        return header("Location: index.php");
    }


//  Dans le cas contraire, 
// Récuperer l'identifiant du film de $_GET
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
// Arrêter automatiquement l'exécution du script
// Rediriger l'utilisateur vers la page d'accueil 
if ($count != 1) {

    return header("Location: index.php");

}

// Dans le cas contraire
// Récuréper le film en question
$film = $req -> fetch();

// Fermer le curseur
$req -> closeCursor();


// Si les données du formulaire ont été enyées via la methode "POST"
if ($_SERVER['REQUEST_METHOD'] == "POST"){
    
    $post_clean = [];
    $edit_form_errors = [];

    // Protéger le serveur contre les failles de type XSS
    foreach ($_POST as $key => $value) {
        $post_clean[$key] = strip_tags(trim($value));
    }
    
    // Mettre en place les contraintes de validation des données du formulaire 
    
    // Pour le nom du film 
    if (isset($post_clean['name'])){

        if ( empty($post_clean['name'])){  // required
            
            $edit_form_errors['name'] = "Le nom du film est obligatoire.";

        } else if (mb_strlen($post_clean['name']) > 255){ // max : 255

            $edit_form_errors['name'] = "Le nom du film doit contenir au maximum 255 caractères";
        }
    }
    
    //  Pour le nom du ou des acteurs
    if ( isset($post_clean['actors'])){

        if (empty($post_clean['actors'])){ // required

            $edit_form_errors['actors'] = "Le nom du ou des acteurs est obligatoire.";

        } else if (mb_strlen($post_clean['actors']) > 255){ // max : 255

            $edit_form_errors['actors'] = "Le nom du ou des acteurs doit contenir maximum 255 caractères.";
        }
    }

    // Pour la note 
    if (isset ($post_clean['review'])){

        if (is_string($post_clean['review']) && ($post_clean['review'] == '')){

            $edit_form_errors['review'] = "La note est obligatoire.";

            // permettre le 0 car sinon 0 = null
        } else if (empty($post_clean['review']) && ($post_clean['review'] != 0)){

            $edit_form_errors['review'] = "La note est obligatoire.";

        } else if ( !is_numeric($post_clean['review'])){

            $edit_form_errors['review'] = "La note est obligatoire.";

        } else if (($post_clean['review'] < 0) || ($post_clean['review'] > 5)){

            $edit_form_errors['review'] = "La note doit être comprise entre 0 et 5.";
        }
    }

    

    //  S'il y a des erreurs,
    if (count($edit_form_errors) > 0){
        
        // Stocker les messages d'erreurs en session
        $_SESSION['edit_form_errors'] = $edit_form_errors;

        // Stocker les données provenant du formulaire en session
        $_SESSION['old'] = $post_clean;

        // Rediriger l'utilisateur vers la page de laquelle proviennent les données
        // J'arrête l'execution du script 
        return header("Location: ". $_SERVER['HTTP_REFERER']); 
    }
    
    
    // Dans le cas contraire,
    

    
    // Protéger le serveur contre les failles de type XSS une seconde fois
    $final_post_clean = [];
    foreach ($post_clean as $key => $value) {
        
        $final_post_clean[$key] = htmlspecialchars($value);
    }

    $film_name = $final_post_clean['name'];
    $film_actors = $final_post_clean['actors'];
    $film_review = $final_post_clean['review'];


    // Arrondir la note à 1 chiffre après la virgule
    $film_review_rounded = round($film_review, 1);
    

    // Etablir une connexion avec la base de données
    require __DIR__ . "/db/connexion.php";

    
    // Effectuer la requête de modification des données dans la table film de la base de données
    $req = $db->prepare("UPDATE film SET name=:name, actors=:actors, review=:review, updated_at=now() WHERE id=:id");

    $req -> bindValue(":name",    $film_name);
    $req -> bindValue(":actors",  $film_actors);
    $req -> bindValue(":review",  $film_review_rounded);
    $req -> bindValue(":id",  $film['id']);
    
    $req -> execute();
    $req -> closeCursor();

    // Génération d'un message flash 
    $_SESSION['success'] = "le film a été modifié avec succès.";


    //  Rediriger l'utilisateur vers la page d'accueil
    // Arreter l'exécution du script
    return header("Location: index.php");

}

?>
<!-- --------------------------------------------View---------------------------- -->
<?php $title = "Modifier ce film"; ?> 

<?php include "partials/head.php"; ?>   

<?php include "partials/nav.php"; ?>

    <!-- Le main represente le contenu spécifique à cette page -->
    <main class="container-fluid">
        <h1>Modifier ce film</h1>

<!-------------------- message d'erreur ----------------->
        <?php if( isset($_SESSION['edit_form_errors']) && !empty($_SESSION['edit_form_errors'])) : ?>
            <div class="alert alert-danger" role="alert">
                <ul>
                    <?php foreach($_SESSION['edit_form_errors'] as $error) : ?>
                        <li> - <?= $error ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
            <?php unset($_SESSION['edit_form_errors']); ?>
        <?php endif ?>


        <div class="form-container">
            <form method="POST">
                <div class="mb-3">
                    <label for="name_film">Nom du film</label>
                    <input type="text" name="name" id="name_film" class="form-control" value="<?php echo isset($_SESSION['old']['name']) ? $_SESSION['old']['name'] : $film['name']; unset($_SESSION['old']['name']);  ?>">
                </div>
                <div class="mb-3">
                    <label for="actors_film">Nom du ou des acteurs</label>
                    <input type="text" name="actors" id="actors_film" class="form-control" value="<?php echo isset($_SESSION['old']['actors']) ? $_SESSION['old']['actors'] : $film['actors'] ; unset($_SESSION['old']['actors']);  ?>">
                </div>
                <div class="mb-3">
                    <label for="review_film">La note sur 5</label>
                    <input type="text" name="review" id="review_film" class="form-control" value="<?php echo isset($_SESSION['old']['review']) ? $_SESSION['old']['review'] : $film['review']; unset($_SESSION['old']['review']);  ?>">
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary">
                </div>
            </form>
        </div>

    </main>

    <?php include "partials/footer.php"; ?>

<?php include "partials/foot.php"; ?>
