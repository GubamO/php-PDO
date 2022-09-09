
<?php
const HOST_NAME	= "mysql";
const DB_NAME	= "weatherapp";
const USER_NAME	= "gubamo";
const PASSWORD	= "Zibulon2303.";
const METEO_TABLE = "Météo";

function bdConnect() {
    try {
        $strConnection = "mysql:host=".HOST_NAME.";dbname=".DB_NAME;
        $arrExtraParam= array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
        $pdo = new PDO($strConnection, USER_NAME, PASSWORD, $arrExtraParam);
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

 ?>