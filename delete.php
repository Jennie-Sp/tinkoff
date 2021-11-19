<?php
include 'setting.php';

$request = json_decode(file_get_contents('php://input'), true);
$query = "DELETE FROM Goods WHERE `id` =".$request['id'];
echo json_encode(["status" => $db->query($query), "query" => $request, "req" => $query]);

