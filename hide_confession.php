<?php
error_reporting(0); // Ekrana PHP hatası yazmasın
header('Content-Type: application/json');

require_once 'config.php';
require_once 'functions.php';

$data = ['success' => false, 'message' => 'Bilinmeyen hata'];

$json = file_get_contents('php://input');
$params = json_decode($json, true);

if (isset($params['confession_id'])) {
    $confession_id = intval($params['confession_id']);

    // Use PDO connection directly
    $stmt = $pdo->prepare("UPDATE confessions SET hidden = 1 WHERE id = ?");
    if ($stmt->execute([$confession_id])) {
        $data['success'] = true;
        $data['message'] = 'İtiraf gizlendi';
    } else {
        $data['message'] = 'Veritabanı hatası';
    }
} else {
    $data['message'] = 'ID bulunamadı';
}

echo json_encode($data);
exit;
