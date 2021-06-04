<?php
require 'utils.php';
if (isset($_GET['action'])){
    switch ($_GET['action']){
        case 'create':
            return create_subs($_GET['id']);
        case 'delete':
            return delete_subs($_GET['id']);
        case 'view':
            return view_subs($_GET['id']);
        case 'update':
            if (isset($_POST['id_abo'],$_POST['id_membre']))
                return modify_subs($_POST['id_abo'],$_POST['id_membre']);
            else
                return die("un argument est manquant");
    }
} else {
    die('l\'argument action est manquant');
}

function create_subs($id){
    $databaseConnexion = create_database_connexion();
    $query = "SELECT * FROM membre WHERE id_fiche_perso = $id LIMIT 1;";
    $request = $databaseConnexion->prepare($query);
    $request->execute();
    $query = "SELECT nom, prix, id_abo, duree_abo FROM abonnement;";
    $request = $databaseConnexion->prepare($query);
    $request->execute();
    if ($request->rowCount() > 0){
        $array_name = [];
        $array_price = [];
        $array_abo_id = [];
        $array_duree = [];
        while($content = $request->fetch()){
            array_push($array_name,$content[0]);
            array_push($array_price,$content[1]);
            array_push($array_abo_id,$content[2]);
            array_push($array_duree,$content[3]);
        }
        echo "<html>
            <form action='partie3.php?action=update' method='POST'>
            <label for='id_abo'>Sélectionner votre nouvelle abonnement.</label>
            <select name='id_abo' id='id_abo'>";
            for ($i = 0; $i < count($array_abo_id); $i++){
                $identifier = $array_abo_id[$i];
                echo "<option value='$identifier'>".$array_name[$i]." price:".$array_price[$i]."€ duree:".$array_duree[$i]." jour(s)</option>";
            }
        echo "</select>
                <input type='hidden' name='id_membre' value='$id'>
                <button type='submit'>Soumettre</button>
                <button type='reset'>Reset</button>
            </form>
        </html>";
    }
}

function view_subs($id){
    $databaseConnexion = create_database_connexion();
    $query = "SELECT id_abo FROM membre WHERE id_fiche_perso = $id AND id_abo <> 0 ;";
    $request = $databaseConnexion->prepare($query);
    if ($request->execute()){
        if ($request->rowCount() > 0){
            $identifier = (int) $request->fetch()[0];
            $query = "SELECT * FROM abonnement WHERE id_abo = $identifier LIMIT 1";
            $req = $databaseConnexion->prepare($query);
            if ($req->execute()){
                $data = $req->fetch();
                echo "<p>Nom de l'abonnement : ".$data['nom']."</p>";
                echo "<p>resumé : ".$data['resum']."</p>";
                echo "<p>prix : ".$data['prix']."€</p>";
            } else {
                die("Une erreur s'est produite. 2");
            }
        } else {
            die("Veuillez souscrire à un abonnement.");
        }
    } else {
        die("Une erreur s'est produite.");
    }
}

function delete_subs($id){
    $databaseConnexion = create_database_connexion();
    $query = "SELECT * FROM membre WHERE id_fiche_perso = $id LIMIT 1;";
    $request = $databaseConnexion->prepare($query);
    if ($request->execute() && $request->rowCount() > 0){
        $query = "UPDATE membre SET id_abo = 0 WHERE id_fiche_perso = $id ;";
        $request = $databaseConnexion->prepare($query);
        if ($request->execute()){
            echo "L'abonnement à bien été supprimé";
        } else {
            echo "Une erreur s'est produite lors de la suppression de votre abonnement.";
        }
    } else {
        die("Vous ne possedez aucun abonnement.");
    }
}

function modify_subs($id,$id_membre){
    $databaseConnexion = create_database_connexion();
    $query = "UPDATE membre SET id_abo = $id WHERE id_fiche_perso = $id_membre ;";
    $request = $databaseConnexion->prepare($query);
    if ($request->execute()){
        echo 'L\'abonnement a bien été modifié';
    } else {
        die("Une erreur s'est produite.");
    }
}