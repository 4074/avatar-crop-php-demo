<?php
include_once('LibImage.php');

$data = $_POST['data'];
$result = LibImage::cropImage($data['source'], $data['position'], '/image/avatar');

echo json_encode($result);
exit;