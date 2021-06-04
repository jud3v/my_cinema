<?php
require 'utils.php';
if (isset($_POST['find']) || isset($_POST['name']) || isset($_POST['forename']) || isset($_GET['name']) || isset($_GET['forename'])) {
    $databaseConnexion = create_database_connexion();
    $name = isset($_POST['name']) ? $_POST['name'] : $_GET['name'];
    $forename = isset($_POST['forename']) ? $_POST['forename'] : $_GET['forename'];
    $limit = get_limit();
    $page = get_page();
    $debut = ($page - 1) * $limit;
    $query = "SELECT nom, prenom, id_perso
    FROM fiche_personne WHERE nom LIKE '%$name%'
    AND prenom LIKE '%$forename%' LIMIT :limite OFFSET :debut";
    $request = $databaseConnexion->prepare($query);
    $request->bindValue('limite',$limit,PDO::PARAM_INT);
    $request->bindValue('debut',$debut,PDO::PARAM_INT);
    $request->execute();
    $previous = $page -1;
    $next = $page + 1;
    if ($request->rowCount() > 0) {
        $array_name = [];
        $array_forename = [];
        $array_id = [];
        while ($content = $request->fetch()) {
            array_push($array_name, $content[0]);
            array_push($array_forename, $content[1]);
            array_push($array_id, $content[2]);
        }
        echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <center>
            <table border="1" s>
    
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Action</th>
                </tr>';
        for($i = 0; $i < count($array_name); $i++){
            $id = $array_id[$i];
            echo "<tr>
                <td>$array_name[$i]</td>
                <td>$array_forename[$i]</td>
                <td>
                    <a href='partie3.php?id=$id&action=view'>Voir abonnement</a>
                    <a href='partie3.php?id=$id&action=delete'>Supprimer l'abonnement</a>
                    <a href='partie3.php?id=$id&action=create'>Créé / Modifier l'abonnement</a>
                    <a href='partie4.php?id=$id&action=history&limit=10'>Historique</a>
                </td>
                </tr>";
        }
        echo "</table>";
        echo create_pagination($next, $previous, "&name=$name&forename=$forename&limit=$limit");
        echo "</center>
    </body>
    </html>";
    } else {
        echo "<center><p>Désolé nous n'avons trouvé aucun résultat pour cette recherche</p></center>";
    }
}
?>

<form method="POST" action="partie2.php">
    <center>
        Entrer le nom du membre : <input type="text" name="name"><br><br>
        Entrer le prénom : <input type="text" name="forename"><br><br>
        Entrer le nombre de résultat : <select name="limit">
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
            <option value="50">50+</option>
        </select><br><br>
        <input type="submit" name="find" value="Rechercher">
        <hr />
    </center>
</form>
</body>

</html>'
