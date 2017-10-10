<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


require './vendor/autoload.php';
require_once './clases/AccesoDatos.php';
require_once './clases/entidades/pizzaApi.php';
require_once './clases/entidades/personaApi.php';
require_once './clases/entidades/usuarioApi.php';
require_once './clases/AutentificadorJWT.php';
require_once './clases/middlewares/MWparaCORS.php';
require_once './clases/middlewares/MWparaAutentificar.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);

/*Usuario*/
$app->group('/usuario', function () {
 
  $this->get('/', \usuarioApi::class . ':traerTodos')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
 
  $this->get('/{username}', \usuarioApi::class . ':traerUno')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

  $this->post('/', \usuarioApi::class . ':CargarUno');

  $this->delete('/', \usuarioApi::class . ':BorrarUno');

  $this->put('/', \usuarioApi::class . ':LlenarBBDD');

});

/*Persona*/
$app->group('/persona', function () {
 
  $this->get('/', \personaApi::class . ':traerTodos')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
 
  $this->get('/{mail}', \personaApi::class . ':traerUno')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

  $this->post('/', \personaApi::class . ':CargarUno');

  $this->delete('/', \personaApi::class . ':BorrarUno')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

  $this->put('/', \personaApi::class . ':ModificarUno');

})->add(\MWparaCORS::class . ':HabilitarCORSTodos');

/*Pizza*/
$app->group('/pizza', function () {
  
   $this->get('/', \pizzaApi::class . ':traerTodos')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
  
   $this->get('/{id}', \pizzaApi::class . ':traerUno')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
 
   $this->post('/', \pizzaApi::class . ':CargarUno');
 
   $this->delete('/', \pizzaApi::class . ':BorrarUno');
 
   $this->put('/', \pizzaApi::class . ':ModificarUno');
 
 })->add(\pizzaApi::class . ':CheckBBDD')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

$app->get('/hola', function (Request $request, Response $response) {
    $response->getBody()->write("hola");

    return $response;
});

$app->run();