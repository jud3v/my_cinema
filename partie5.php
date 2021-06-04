<?php
require 'utils.php';
if (isset($_GET['id'])){
    switch ($_GET['action']){
        case 'entry':
            return show_film($_GET['id']);
        case 'add':
            return add_film_to_history($_GET['id'], $_GET['film']);
        case 'back':
            return_redirect_back();
    }
}

function show_film($id)
{
    $limit = get_limit();
    $databaseConnexion = create_database_connexion();
    $request = $databaseConnexion->prepare("SELECT id_film, titre FROM film GROUP BY titre, id_film LIMIT :limit OFFSET :debut;");
    $request->bindValue('limit', $limit, PDO::PARAM_INT);
    $request->bindValue('debut', (get_page() - 1) * $limit, PDO::PARAM_INT);
    if ($request->execute()){
        echo "<form action='partie5.php?action=entry&id=$id' method='POST'>
            <label for='limit'>Sélectionner le nombre d'entrée à afficher par page</label><br>
            <select name='limit' id='limit'>
                <option value='10'>10</option>
                <option value='20'>20</option>
                <option value='30'>30</option>
                <option value='40'>40</option>
                <option value='50'>50</option>
            </select>
            <button type='submit'>Soumettre</button></form>";
        echo create_pagination(get_page() + 1, get_page() - 1, "&action=entry&id=$id&limit=$limit");
        echo "<ul>";
        foreach ($request->fetchAll() as $data){
            $title = $data[1];
            $filmId = $data[0];
            echo "<li>$title <a href='partie5.php?action=add&id=$id&film=$filmId'>Ajouter le film à l'historique du membre</a></li>";
        }
        echo "</ul>";
    } else {
        die("Une erreur s'est produite");
    }
}

function add_film_to_history(int $id, int $film){
    $databaseConnexion = create_database_connexion();
    $request = $databaseConnexion->prepare("INSERT INTO historique_membre VALUES ($id, $film, NOW())");
    if ($request->execute()){
        echo "<h3>Le film a bien été ajouté à l'historique</h3>";
        $back = $_SERVER['HTTP_REFERER'];
        echo "<a href='$back'>retour en arrière</a>";
    } else {
        die("Une erreur s'est produite");
    }
}