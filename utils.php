<?php

if (! function_exists('create_database_connexion')){
    /**
     * This function will create and return a new database connexion.
     * @return PDO
     */
    function create_database_connexion(): PDO
    {
        try {
            return new PDO("mysql:host=127.0.0.1;dbname=cinema", 'judikael', 'judikael');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}

if (! function_exists('create_pagination')){
    /**
     * This function will return you the pagination button
     * @param int $next
     * @param int $previous
     * @param string $param
     * @return string
     */
    function create_pagination(int $next, int $previous, string $param): string {
        return "<a href='?page=$previous$param'>Page précédente</a>
        <a href='?page=$next$param'>Page suivante</a>";
    }
}

if (! function_exists('show_history_query')){
    /**
     * Return the sql query for show history
     * @param int $id
     * @return string
     */
    function show_history_query(int $id): string {
        return "SELECT titre, date, film.id_film FROM film INNER JOIN historique_membre
                INNER JOIN membre ON historique_membre.id_film = film.id_film
                AND membre.id_membre = historique_membre.id_membre WHERE membre.id_membre = $id LIMIT :limit OFFSET :debut";
    }
}

if (! function_exists('get_limit')){
    /**
     * This function return the limit number param
     * @return int
     */
    function get_limit(): int {
        if (isset($_POST['limit'])){
            $limit = $_POST['limit'];
        } elseif (isset($_GET['limit'])){
            $limit = $_GET['limit'];
        } else {
            $limit = 10;
        }

        return (int) $limit;
    }
}

if (! function_exists('get_page')){
    /**
     * This function return the page number param
     * @return int
     */
    function get_page(): int {
        return (!empty($_GET['page']) ? $_GET['page'] : 1);
    }
}

if (! function_exists('return_redirect_back')){
    /**
     *  This function will redirect you to referer
     */
    function return_redirect_back(){
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

if (! function_exists('check_table_exist')){
    /**
     * This function check if a table exist
     * @param $table
     * @return bool
     */
    function check_table_exist($table): bool {
        $databaseConnexion = create_database_connexion();
        $request = $databaseConnexion->prepare("SHOW TABLES");
        if ($request->execute()){
            while($content = $request->fetch()){
                if ($content[0] == $table){
                    return true;
                }
            }
        }
        return false;
    }
}

if (! function_exists('create_avis_table')){
    /**
     * This function will create "avis" table if "avis" table not exist.
     * @return bool
     */
    function create_avis_table(): bool {
        if (! check_table_exist("avis")){
            $databaseConnexion = create_database_connexion();
            $query = "CREATE TABLE avis (id_avis int PRIMARY KEY, id_fiche_membre bigint NOT NULL, id_film bigint NOT NULL, description text NOT NULL, date datetime NOT NULL);";
            $request = $databaseConnexion->prepare($query);
            if ($request->execute()){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}