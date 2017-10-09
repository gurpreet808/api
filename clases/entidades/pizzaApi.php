<?php
require_once 'pizza.php';
require_once 'IApiUsable.php';

class pizzaApi extends pizza implements IApiUsable{
	public function TraerUno($request, $response, $args) {

		try {
			pizza::TraerTodasLasPizzas();
		} catch (Exception $e) {			
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
			$consulta =$objetoAccesoDato->RetornarConsulta("CREATE TABLE `pizzas` (
				`id_pizza` int NOT NULL AUTO_INCREMENT, 
				`sabor` varchar(30) NOT NULL, 
				`tipo` varchar(10) NOT NULL, 
				`cantidad` int NOT NULL, 
				`foto` varchar(80), 
				PRIMARY KEY (`id_pizza`)
			)");
			
			return $consulta->execute();			
		}
	   $newResponse = $response->withJson($laPizza, 200);

	   return $newResponse;
   }
	
	public function TraerUno($request, $response, $args) {
     	$id=$args['id'];
    	$laPizza=pizza::TraerUnaPizza($id);
		$newResponse = $response->withJson($laPizza, 200);

    	return $newResponse;
    }
    
    public function TraerTodos($request, $response, $args) {
		$todasLasPizzas=pizza::TraerTodasLasPizzas();
		$newResponse = $response->withJson($todasLasPizzas, 200);  

	   return $newResponse;
    }
    
    public function CargarUno($request, $response, $args) {
		$ArrayDeParametros = $request->getParsedBody();
        //var_dump($ArrayDeParametros);

		$newResponse = $response;
        
		if (!array_key_exists('sabor', $ArrayDeParametros) 
        or !array_key_exists('tipo', $ArrayDeParametros)
        or !array_key_exists('cantidad', $ArrayDeParametros)) {
			$rta = '<p>Ingrese todas las keys ("sabor", "tipo", y "cantidad")</p>';
		} else {
			if ($ArrayDeParametros['sabor']==null
            or $ArrayDeParametros['cantidad']==null
            or $ArrayDeParametros['tipo']==null) {
				$rta = '<p>ERROR!! Ingrese todos los datos ("sabor", "tipo" y "cantidad")</p>';
			}else {
				$mipizza = new pizza();
				
				$mipizza->sabor=$ArrayDeParametros['sabor'];
                $mipizza->tipo=$ArrayDeParametros['tipo'];
                $mipizza->cantidad=$ArrayDeParametros['cantidad'];
				//$mipizza->foto=$ArrayDeParametros['foto'];
                
				$rta = $mipizza->GuardarPizza();
			}	
		}
		$newResponse->getBody()->write($rta);

        return $newResponse;
    }

    public function BorrarUno($request, $response, $args) {
		$newResponse = $response;
		
		$ArrayDeParametros = $request->getParsedBody();
		$id=$ArrayDeParametros['id'];
			
		$cantidadDeBorrados = pizza::BorrarPizzaPorId($id);

		if($cantidadDeBorrados>0){
			//$newResponse = $newResponse->withAddedHeader('alertType', "success");
			$rta = "Elementos borrados: ".$cantidadDeBorrados;
		}
		else {
			//$newResponse = $newResponse->withAddedHeader('alertType', "danger");
			$rta = "No se borró nada";	
		}

		$newResponse->getBody()->write($rta);
		return $newResponse;
    }
     
    public function ModificarUno($request, $response, $args) {
		$newResponse = $response;
		
		$ArrayDeParametros = $request->getParsedBody();
		$newResponse = $response;

		if (!array_key_exists('sabor', $ArrayDeParametros)
		or !array_key_exists('foto', $ArrayDeParametros)
		or !array_key_exists('cantidad', $ArrayDeParametros) 
		or !array_key_exists('tipo', $ArrayDeParametros)) {
			$newResponse = $newResponse->withAddedHeader('alertType', "warning");
			$rta = '<p>Ingrese todas las keys ("sabor", "tipo", "cantidad" y "foto")</p>';
		} else {
			if ($ArrayDeParametros['sabor']==null
			or $ArrayDeParametros['foto']==null
			or $ArrayDeParametros['cantidad']==null
			or $ArrayDeParametros['tipo']==null) {
				$newResponse = $newResponse->withAddedHeader('alertType', "danger");
				$rta = '<p>ERROR!! Ingrese todos los datos ("sabor", "cantidad", "foto" y "tipo")</p>';
			}else {
				$mipizza->sabor=$ArrayDeParametros['sabor'];
				$mipizza->foto=$ArrayDeParametros['foto'];
				$mipizza->tipo=$ArrayDeParametros['tipo'];
				$mipizza->tipo=$ArrayDeParametros['cantidad'];
				$guardo = true;

				//$newResponse = $newResponse->withAddedHeader('alertType', "success");
				if ($guardo) {
					if ($mipizza->ModificarPizza()>0) {
						$rta = "pizza modificada";
					} else {
						$rta = "No se modificó la pizza";
					}
				}					
			}	
		}	

		$newResponse->getBody()->write($rta);

        return $newResponse;
    }

}
?>