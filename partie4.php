<?php
require 'utils.php';
if ($_GET['action']){
    switch ($_GET['action']){
        case 'history':
            if (isset($_GET['id'])){
                return show_history($_GET['id']);
            } else {
                die('L\'argument id est requis.');
            }
    }
}

function show_history($id){
    $limit = get_limit();
    $debut = (get_page() - 1) * $limit;
    $databaseConnexion = create_database_connexion();
    $request = $databaseConnexion->prepare(show_history_query($id));
    $request->bindValue('limit',$limit,PDO::PARAM_INT);
    $request->bindValue('debut',$debut,PDO::PARAM_INT);
    if ($request->execute()){
        echo '<ul>';
        echo '<h3>Les films que vous avez déjà vus</h3>';
        echo "<a href='partie5.php?id=$id&action=entry'>Ajouter une entrée dans l'historique du membre</a>";
        echo "<form action='partie4.php?action=history&id=$id' method='POST'>
            <label for='limit'>Sélectionner le nombre d'entrée à afficher par page</label><br>
            <select name='limit' id='limit'>
                <option value='10'>10</option>
                <option value='20'>20</option>
                <option value='30'>30</option>
                <option value='40'>40</option>
                <option value='50'>50</option>
            </select>
            <button type='submit'>Soumettre</button></form>";
        echo create_pagination(get_page() + 1, get_page() - 1, "&action=history&id=$id&limit=$limit");
        foreach ($request->fetchAll() as $data){
            $titre = $data['titre'];
            $date = $data['date'];
            $identifier = $data['id_film'];
            echo "<li>$titre le $date
                        <a href='partie6.php?action=add&id_member=$id&film=$identifier'>Laisser un avis</a>
                        <a href='partie6.php?action=view&id_member=$id&film=$identifier'>Voir l'avis</a>
                        <a href='partie6.php?action=update&id_member=$id&film=$identifier'>Modifier l'avis</a>
                        <a href='partie6.php?action=delete&id_member=$id&film=$identifier'>Supprimer l'avis</a>
                    </li>";
        }
        echo '</ul>';
    } else {
        die("Une erreur s'est produite.");
    }
}