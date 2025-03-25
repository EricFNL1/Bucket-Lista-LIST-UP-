<?php
require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

// Configurações do projeto e bucket
$projectId = 'absolute-brook-452223-g6';
$keyFilePath = __DIR__ . '/service-account.json';
$bucketName = 'aula-apresentacao';

// Instancia o cliente do Storage
$storage = new StorageClient([
    'projectId'   => $projectId,
    'keyFilePath' => '/var/www/html/Bucket-Lista-LIST-UP-/service-account.json'
]);

$bucket = $storage->bucket($bucketName);

// Recebe os parâmetros via GET
$fileName = $_GET['fileName'] ?? null;
$contentType = $_GET['contentType'] ?? null;

header('Content-Type: application/json');

if (!$fileName || !$contentType) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros fileName e contentType são obrigatórios.']);
    exit;
}

$object = $bucket->object($fileName);

// Define o tempo de expiração para 15 minutos a partir de agora
$expires = new DateTime('+15 minutes');

// Configura as opções da URL assinada para upload (método PUT)
$options = [
    'version'     => 'v4',
    'method'      => 'PUT',
    'contentType' => $contentType,
];

try {
    $url = $object->signedUrl($expires, $options);
    echo json_encode(['url' => $url]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao gerar URL assinada', 'details' => $e->getMessage()]);
}
