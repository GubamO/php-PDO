
<?php

const HOST_NAME	= "mysql";
const DB_NAME	= "weatherapp";
const USER_NAME	= "root";
const PASSWORD	= "";
const METEO_TABLE = "Météo";

function bdConnect() {
    try {
        $strConnection = "mysql:host=".HOST_NAME.";dbname=".DB_NAME;
        $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
        $pdo = new PDO($strConnection, USER_NAME, PASSWORD, $arrExtraParam); // Instanciate connexion
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
    catch(PDOException $e) {
        $msg = 'ERREUR PDO dans ' . $e->getFile() . ' L.' . $e->getLine() . ' : ' . $e->getMessage();
        die($msg);
    }
}

function buildCheckBox($data) {
    return "<input type='checkbox' name='delete-city[]' value='$data[ville]'>";
}

function showMeteoTable() {

    $meteoTable = METEO_TABLE;

    $pdo = bdConnect();

    if ($pdo) {
        $query = "DESCRIBE $meteoTable;";
        $columnNames = $pdo->query($query)->fetchAll();

        echo "<table><tr>";

        foreach ($columnNames as $value) {
            echo "<th>$value[0]</th>";
        }

        $columnNames = NULL;

        echo "</tr>";

        $query = "SELECT ville, haut, bas FROM Météo;";
        $meteoValues = $pdo->query($query)->fetchALL();

        foreach ($meteoValues as $value) {
            echo "<tr><td>".$value['ville']."</td><td>".$value['haut']."</td><td>".$value['bas']."</td><td>".buildCheckBox($value)."</td></tr>";
        }

        $meteoValues = NULL;
        $pdo = NULL;
    }

}

function deleteEntry($cities) {
    $pdo = bdConnect();

    foreach ($cities as $city) {
        $query = $pdo->prepare("DELETE FROM ".METEO_TABLE." WHERE ville = :city;");
        $query->execute([
            ":city" => "$city",
        ]);
    }

    $pdo = NULL;
    $query = NULL;
}

function addEntry($data) {
    $pdo = bdConnect();

    if ($pdo) {
        $query = $pdo->prepare("INSERT INTO ".METEO_TABLE." (ville, haut, bas) VALUES (:city, :max, :min) ON DUPLICATE KEY UPDATE ville = :city, haut = :max, bas = :min;");
        $query->execute([
            ":city" => "$data[city]",
            ":min" => "$data[min]",
            ":max" => "$data[max]",
        ]);

        $pdo = NULL;
        $query = NULL;
    }
}

function start() {
    $deleteCities = (isset($_POST['delete-city'])) ? $_POST['delete-city'] : NULL;

    // delete selected entries
    if ($deleteCities) {
        deleteEntry($deleteCities);
    }

    // Add an entry
    if (!empty($_POST['city']) && !empty($_POST['max']) && !empty($_POST['min']) ) {

        $data = [
            "city" => $_POST['city'],
            "max" => $_POST['max'],
            "min" => $_POST['min'],
        ];

        addEntry($data);
    }

    showMeteoTable();
}

?>

<form method="post" action="">
    <label for="city">Ville</label>
    <input type="text" id="city" name="city"><br><br>

    <label for="max">Max temperature</label><br>
    <input type="number" id="max" name="max"><br>

    <label for="min">Min temperature</label><br>
    <input type="number" id="min" name="min"><br>

    <input type="submit" name="submit" value="Add / Update">


    <?php start() ?>

</form>
