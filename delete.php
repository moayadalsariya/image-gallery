<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /** @var \PDO */
    require_once "./database.php";
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $statment = $pdo->prepare("DELETE FROM gallery WHERE id = :id;");
        $statment->bindValue(':id', $id);
        $statment->execute();
    }
}
header("Location: ./index.php");
exit();
