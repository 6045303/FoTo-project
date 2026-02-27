<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: index.php');
    exit;
}

function table_exists($mysqli, $table){
    $t = $mysqli->real_escape_string($table);
    $res = $mysqli->query("SHOW TABLES LIKE '".$t."'");
    if (! $res) return false;
    $exists = $res->num_rows > 0;
    $res->free();
    return $exists;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0){
    if (table_exists($mysqli, 'bookings')){
        $stmt = $mysqli->prepare('DELETE FROM bookings WHERE id = ?');
        if ($stmt){
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    }
    // also attempt to delete from legacy table 'activiteiten' if present
    if (table_exists($mysqli, 'activiteiten')){
        $stmt2 = $mysqli->prepare('DELETE FROM activiteiten WHERE id = ?');
        if ($stmt2){
            $stmt2->bind_param('i', $id);
            $stmt2->execute();
            $stmt2->close();
        }
    }
}

header('Location: index.php');
exit;

?>
