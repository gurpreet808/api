<?php


class MWcheckBBDD
{
	public function HabilitarCORSTodos($request, $response, $next) {
		/*
		al ingresar no hago nada
		*/
		 $response = $next($request, $response);
		 //solo afecto la salida con los header
		// $response->getBody()->write('<p>habilitado HabilitarCORSTodos</p>');
   		 return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}