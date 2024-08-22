<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Silex\Provider\MonologServiceProvider;

$_POST = json_decode(file_get_contents("php://input"), true);

$app = new Silex\Application();
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../debug.log',
));


/**
 * Saving request to the LogStash
 * @param string $crudMethod
 * @param array $request
 * @return void
 * @throws GuzzleException
 */
function requestToLogstashAPI(string $crudMethod = "create", array $request = []): void
{
    $client = new Client([
        'base_uri' => 'http://logstash:5043/',
        'timeout' => 2.0,
    ]);
    $client->post('/', ['json' => [
        'method' => $_SERVER['REQUEST_METHOD'],
        'crud' => $crudMethod,
        'url' => $_SERVER['REQUEST_URI'],
        'request' => $request
    ]]);
}


/**
 * Simple CRUD API fake
 */
$app->post("/products", function () use ($app) {
    requestToLogstashAPI("CRUD_CREATE", $_POST);
    //...... fake actions .... //
    $app['monolog']->info("New Product being created");
    return $app->json(['message' => 'Product being created', 'product'=>$_POST]);
});



// GET:/products
$app->get("/products", function () use ($app) {
    requestToLogstashAPI("CRUD_READ_ALL");
    //...... fake actions .... //
    return $app->json(['some' => 'response']);
});



// GET:/products/:id
$app->get("/products/{id}", function (int $id) use ($app) {
    requestToLogstashAPI("CRUD_READ_ONE");
    //...... fake actions .... //
    return $app->json(['some' => 'response']);
});



// PUT:/products/:id
$app->put("/products/{id}", function (int $id) use ($app) {
    requestToLogstashAPI("CRUD_UPDATE", $_POST);
    //...... fake actions .... //
    return $app->json(['some' => 'response']);
});



// DELETE:/products/:id
$app->delete("/products/{id}", function (int $id) use ($app) {
    requestToLogstashAPI("CRUD_DELETE");
    //...... fake actions .... //
    $app['monolog']->info("Product being deleted");
    return $app->json(['message' => 'Product was deleted']);
});



$app->run();