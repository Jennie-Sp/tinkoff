<?php
include 'setting.php';

if (isset($_FILES['image'])) {
    $path = 'files/' . basename($_FILES['image']['name']);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
        $query = "INSERT INTO Goods VALUES (" . $_POST["id"] . ", '" . $_POST["name"] . "','" . $_POST["description"] . "'," . $_POST['price'] . ", '" . $path . "', " . $_POST['quantity'] . ")";
        $_POST['path'] = $path;
        echo json_encode(["status" => $db->query($query), "query" => $_POST]);
    }
} else {
    $query = "INSERT INTO Goods VALUES (" . $_POST["id"] .", '" . $_POST["name"] . "','" . $_POST["description"] . "'," . $_POST['price'] . ", 'null', " . $_POST['quantity'] . ")";
    echo json_encode(["status" => $db->query($query), "query" => $_POST]);
}

