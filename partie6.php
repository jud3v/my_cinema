<?php
require 'utils.php';

if (isset($_GET['action'])){
    switch ($_GET['action']){
        case 'add':
            show_form($_GET['id_member'],$_GET['film']);
            break;
        case 'view':
            view_avis($_GET);
            break;
        case 'update':
            update_avis($_GET);
            break;
        case 'push':
            push_update_avis($_POST);
            break;
        case 'delete':
            delete_avis($_GET);
            break;
    }
} elseif(isset($_POST['description'])) {
    return create_avis($_POST);
} else {
    die('Les arguments requis sont manquant');
}

function show_form(int $id_member, int $id_film){
    echo "<form action='partie6.php' method='POST'>
            <input type='hidden' name='id_member' value='$id_member'>
            <input type='hidden' name='id_film' value='$id_film'>
            <label for='description'>Insérer l'avis</label><br>
            <textarea cols='30' id='description' rows='30' required name='description'></textarea><br>
            <button type='submit'>Soumettre l'avis</button>
            <button type='reset'>Reinitialiser le champs</button>
        </form>";
}

function create_avis(array $data){
    $id_member = $data['id_member'];
    $id_film = $data['id_film'];
    $description = $data['description'];
    $databaseConnexion = create_database_connexion();
    $request = $databaseConnexion->prepare("SELECT * FROM historique_membre WHERE id_membre = $id_member AND $id_film = $id_film");
    if ($request->execute() && $request->rowCount() > 0){
        $query = "UPDATE historique_membre SET avis = '$description' WHERE id_film = $id_film AND id_membre = $id_member LIMIT 1";
        $request = $databaseConnexion->prepare($query);
        if ($request->execute()){
            die ("L'avis à bien été enregistré");
        } else {
            die("Une erreur s'est produite");
        }
    } else {
        $query = "INSERT INTO historique_membre VALUES ($id_member, $id_film, NOW(),'$description')";
        $request = $databaseConnexion->prepare($query);
        if ($request->execute()){
            die ("L'avis à bien été enregistré");
        } else {
            die("Une erreur s'est produite");
        }
    }
}

function view_avis(array $data){
    $id_member = $data['id_member'];
    $id_film = $data['film'];
    $database = create_database_connexion();
    $req = $database->prepare("SELECT avis FROM historique_membre WHERE id_film = $id_film AND id_membre = $id_member AND avis IS NOT NULL");
    if($req->execute() && $req->rowCount() > 0){
        echo $req->fetch()[0];
    } else {
        echo "Aucun avis n'a été trouvé.";
    }
}

function update_avis(array $data){
    $id_member = $data['id_member'];
    $id_film = $data['film'];
    $database = create_database_connexion();
    $req = $database->prepare("SELECT avis FROM historique_membre WHERE id_film = $id_film AND id_membre = $id_member ");
    if($req->execute()){
        $avis = $req->fetch()[0];
        echo "<form action='partie6.php?action=push' method='POST'>
                <label for='avis'>Modification de votre avis</label><br>
                <input type='hidden' name='id_member' value='$id_member'>
                <input type='hidden' name='id_film' value='$id_film'>
                <textarea cols='10' rows='10' name='avis' id='avis'>$avis</textarea><br>
                <button type='submit'>Soumettre la modification</button>
                <button type='reset'>Réinitialiser le champs</button>
            </form>";
    } else {
        echo "Aucun avis n'a été trouvé.";
    }
}

function push_update_avis($data){
    $id_member = $data['id_member'];
    $id_film = $data['id_film'];
    $avis = $data['avis'];
    $database = create_database_connexion();
    $req = $database->prepare("UPDATE historique_membre SET avis = '$avis' WHERE id_film = $id_film AND id_membre = $id_member ");
    if($req->execute()){
        echo "L'avis a bien été mis à jour";
    } else {
        echo "Aucun avis n'a été trouvé.";
    }
}

function delete_avis($data){
    $id_member = $data['id_member'];
    $id_film = $data['film'];
    $database = create_database_connexion();
    $req = $database->prepare("UPDATE historique_membre SET avis = NULL WHERE id_film = $id_film AND id_membre = $id_member ");
    if($req->execute()){
        echo "L'avis a bien été supprimé";
    } else {
        echo "Aucun avis n'a été trouvé.";
    }
}