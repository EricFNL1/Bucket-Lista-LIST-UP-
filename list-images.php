<?php
require 'vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;

// Configurações do projeto e bucket
$projectId = 'absolute-brook-452223-g6';
$keyFilePath = __DIR__ . '/service-account.json';
$bucketName = 'aula-apresentacao';

$storage = new StorageClient([
    'projectId'   => 'SEU_PROJECT_ID',
    'keyFilePath' => __DIR__ . '/service-account.json'
]);



$bucket = $storage->bucket($bucketName);

// Lista todos os objetos no bucket
$objects = $bucket->objects();
$images = [];

foreach ($objects as $object) {
    // Define o tempo de expiração para 15 minutos
    $expires = new DateTime('+15 minutes');
    $options = [
        'version' => 'v4',
        'method'  => 'GET'
    ];
    
    try {
        $url = $object->signedUrl($expires, $options);
        $images[] = [
            'name' => $object->name(),
            'url'  => $url,
        ];
    } catch (Exception $e) {
        // Se ocorrer algum erro ao gerar a URL para um objeto, pode ser logado ou tratado
        error_log('Erro ao gerar URL para ' . $object->name() . ': ' . $e->getMessage());
    }
}

header('Content-Type: application/json');
echo json_encode(['images' => $images]);
