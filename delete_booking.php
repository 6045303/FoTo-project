<?php
require_once __DIR__ . '/classes/ActivityModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
    $model = new ActivityModel();
    $model->delete($id);
}

header('Location: index.php');
exit;