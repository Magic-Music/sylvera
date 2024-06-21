<?php declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Osteel\OpenApi\Testing\ValidatorBuilder;
use Symfony\Component\HttpFoundation\Response;

$yamlFile = __DIR__ . '/spec.yaml';
$validator = ValidatorBuilder::fromYamlFile($yamlFile)->getValidator();

try {
    $validator->validate(
        message: new Response(
            content: file_get_contents('http://localhost:8080/wp-json/sylvera/v1/projects/28'),
            status: 200,
            headers: ['Content-Type' => 'application/json']
        ),
        path: '/wp-json/sylvera/v1/projects/28',
        method: 'get'
    );
    $validator->validate(
        message: new Response(
            content: file_get_contents('http://localhost:8080/wp-json/sylvera/v1/projects'),
            status: 200,
            headers: ['Content-Type' => 'application/json']
        ),
        path: '/wp-json/sylvera/v1/projects',
        method: 'get'
    );
    echo 'OK' . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
