<form method="POST" action="partie1.php">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <center>
        Entrer le titre du film : <input type="text" name="titre"><br><br>
        Entrer le genre du film : <input type="text" name="genre"><br><br>
        Entrer le distributeur du film : <input type="text" name="distributeur"><br><br>
        Entrer la date de projection : <input type="datetime-local" name="date"><br><br>
        Entrer le nombre de résultat : <select name="limit">
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
            <option value="50">50+</option>
        </select><br><br>
        <input type="submit" name="find" value="Rechercher">
        <input type="reset" value="Reset">
        <hr />
    </center>
</form>
</body>
</html>
<?php
require 'utils.php';
if (isset($_POST['find']) || isset($_POST['titre']) || isset($_POST['genre']) || isset($_POST['distributeur']) ||
    isset($_GET['titre']) || isset($_GET['genre']) || isset($_GET['distributeur'])) {
    $databaseConnexion = create_database_connexion();
    $titre = isset($_POST['titre']) ? $_POST['titre'] : $_GET['titre'];
    $genre = isset($_POST['genre']) ? $_POST['genre'] : $_GET['genre'];
    $distributeur = isset($_POST['distributeur']) ? $_POST['distributeur'] : $_GET['distributeur'];
    $date = isset($_POST['date']) ? $_POST['date'] : '';
    str_replace('T',' ', $date);
    $limit = get_limit();
    $page = get_page();
    $debut = ($page - 1) * $limit;
    $query = "SELECT film.titre, g.nom, d.nom
    FROM film  INNER JOIN genre AS g  INNER JOIN distrib as d 
    ON film.id_genre = g.id_genre AND film.id_distrib = d.id_distrib
    WHERE film.titre LIKE '%$titre%'
    AND g.nom LIKE '%$genre%'
    AND d.nom LIKE '%$distributeur%'
    AND film.date_debut_affiche > '$date' LIMIT :limite OFFSET :debut";
    $request = $databaseConnexion->prepare($query);
    $request->bindValue('limite',$limit,PDO::PARAM_INT);
    $request->bindValue('debut',$debut,PDO::PARAM_INT);
    $request->execute();
    $previous = $page -1;
    $next = $page + 1;
    if ($request->rowCount() > 0) {
        $array_titre = [];
        $array_genre = [];
        $array_distributeur = [];
        while ($content = $request->fetch()) {
            array_push($array_titre, $content[0]);
            array_push($array_genre, $content[1]);
            array_push($array_distributeur, $content[2]);
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
        <h3>Résultat:</h3>
            <table border="1" s>
    
                <tr>
                    <th> Titre : </th>
                    <th> Genre : </th>
                    <th> Distributeur :</th>
                </tr>';
                for($i = 0; $i < count($array_titre); $i++){
                    echo "<tr>".
                        '<td>'. $array_titre[$i]. '</td>'.
                        '<td>'. $array_genre[$i]. '</td>'.
                        '<td>'. $array_distributeur[$i]. '</td>'.
                        "</tr>";
                }
    echo "</table>";
    echo create_pagination($next,$previous,"&genre=$genre&titre=$titre&distributeur=$distributeur&limit=$limit");
    echo "</center>
    </body>
    </html>";
        } else {
            echo "<center><p>Désolé nous n'avons trouvé aucun résultat pour cette recherche</p></center>";
        }
}
?>