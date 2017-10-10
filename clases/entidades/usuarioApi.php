<?php
require_once 'usuario.php';
require_once 'IApiUsable.php';

class usuarioApi extends Usuario implements IApiUsable{

	public function CheckBBDD($request, $response, $next) {
		$newResponse = $response;

		try {
			usuario::TraerTodosLosUsuarios();
			$newResponse = $next($request, $response);
			
		} catch (Exception $e) {
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
			$consulta =$objetoAccesoDato->RetornarConsulta("CREATE TABLE `usuarios` (
				`id_usuario` int NOT NULL AUTO_INCREMENT, 
				`nombre` varchar(30) NOT NULL, 
				`apellido` varchar(30) NOT NULL, 
				`mail` varchar(50) NOT NULL UNIQUE,
				`username` varchar(20) NOT NULL UNIQUE, 
				`password` varchar(20) NOT NULL, 
				`habilitado` int NOT NULL,  
				`foto` varchar(80), 
				PRIMARY KEY (`id_usuario`)
			)");
			
			$newResponse->getBody()->write($consulta->execute());
		}
		return $newResponse;
	}
	
	public function LlenarBBDD(){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		$consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO usuarios (nombre,apellido,mail,username,password,habilitado)
	 		values
			 ('administrador','administrator','admin@admin.com','admin','admin',0),
			 ('usuario','user','user@user.com','user','user',0), 
			 ('Pepe','López','pepe@pepe.com','pepe2017','pepe2017',0)");
	
		return $consulta->execute();
	}
 	public function TraerUno($request, $response, $args) {
     	$username=$args['username'];
		$elUsuario=Usuario::TraerUnUsuario($username);
		
		$newResponse = $response;
		
		if (!$elUsuario) {
			return $newResponse->getBody()->write('<p>ERROR!! No se encontró ese usuario.</p>');			
		}	

    	return $newResponse->withJson($elUsuario, 200);
		//$newResponse = $newResponse->withAddedHeader('Token', 'unTokenCreado');

    	return $newResponse;
    }
    
    public function TraerTodos($request, $response, $args) {
      	$todosLosUsuarios=Usuario::TraerTodosLosUsuarios();
     	$newResponse = $response->withJson($todosLosUsuarios, 200);  
    	//$newResponse = $newResponse->withAddedHeader('Token', 'unTokenCreado');

        return $newResponse;
    }
    
    public function CargarUno($request, $response, $args) {
		$ArrayDeParametros = $request->getParsedBody();
        //var_dump($ArrayDeParametros);

		$newResponse = $response;
        
		if (!array_key_exists('nombre', $ArrayDeParametros) 
        or !array_key_exists('apellido', $ArrayDeParametros) 
        or !array_key_exists('mail', $ArrayDeParametros) 
        or !array_key_exists('username', $ArrayDeParametros) 
        or !array_key_exists('password', $ArrayDeParametros) 
        or !array_key_exists('habilitado', $ArrayDeParametros)) {
			$rta = '<p>Ingrese todas las keys ("nombre", "apellido", "mail", "username", "password" y "habilitado")</p>';
		} else {
			if ($ArrayDeParametros['nombre']==null 
            or $ArrayDeParametros['apellido']==null 
            or $ArrayDeParametros['mail']==null 
            or $ArrayDeParametros['username']==null
            or $ArrayDeParametros['password']==null
            or $ArrayDeParametros['habilitado']==null) {
				$rta = '<p>ERROR!! Ingrese todos los datos ("nombre", "apellido", "mail", "username", "password" y "habilitado")</p>';
			}else {
				$miUsuario = new Usuario();
				
				$miUsuario->nombre=$ArrayDeParametros['nombre'];
				$miUsuario->apellido=$ArrayDeParametros['apellido'];
				$miUsuario->mail=$ArrayDeParametros['mail'];
				$miUsuario->username=$ArrayDeParametros['username'];
                $miUsuario->setPassword($ArrayDeParametros['password']);
				$miUsuario->habilitado=$ArrayDeParametros['habilitado'];
				if (!array_key_exists('foto', $ArrayDeParametros) && $ArrayDeParametros['foto']!=null) {
					# code...
				}
                
				$rta = $miUsuario->GuardarUsuario();
			}	
		}
		$newResponse->getBody()->write($rta);

        return $newResponse;
    }

    public function BorrarUno($request, $response, $args) {
		$newResponse = $response;
		
		$ArrayDeParametros = $request->getParsedBody();
		$username=$ArrayDeParametros['username'];
			
		$cantidadDeBorrados = Usuario::BorrarUsuarioPorUsername($username);

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

		if (!array_key_exists('nombre', $ArrayDeParametros) 
		or !array_key_exists('apellido', $ArrayDeParametros) 
		or !array_key_exists('mail', $ArrayDeParametros) 
		or !array_key_exists('username', $ArrayDeParametros) 
		or !array_key_exists('habilitado', $ArrayDeParametros)) {
			$newResponse = $newResponse->withAddedHeader('alertType', "warning");
			$rta = '<p>Ingrese todas las keys ("nombre", "apellido", "mail", "username" y "habilitado")</p>';
		} else {
			if ($ArrayDeParametros['nombre']==null 
			or $ArrayDeParametros['apellido']==null 
			or $ArrayDeParametros['mail']==null 
			or $ArrayDeParametros['username']==null
			or $ArrayDeParametros['habilitado']==null) {
				$newResponse = $newResponse->withAddedHeader('alertType', "danger");
				$rta = '<p>ERROR!! Ingrese todos los datos ("nombre", "apellido", "mail", "username" y "habilitado")</p>';
			}else {
				$miUsuario = Usuario::TraerUnUsuario($ArrayDeParametros['username']);
				if(empty($miUsuario)){
					$rta = "No se encontró el username ingresado";
				} else {
					$miUsuario->nombre=$ArrayDeParametros['nombre'];
					$miUsuario->apellido=$ArrayDeParametros['apellido'];
					$miUsuario->mail=$ArrayDeParametros['mail'];
					$miUsuario->habilitado=$ArrayDeParametros['habilitado'];
					$guardo = true;

					if (array_key_exists('nuevoUsername', $ArrayDeParametros) && $ArrayDeParametros['nuevoUsername']!=null) {
						if(empty(Usuario::TraerUnUsuario($ArrayDeParametros['nuevoUsername']))){
							$miUsuario->username=$ArrayDeParametros['nuevoUsername'];
						} else {
							$rta = "Ya hay un usuario con ese username";
							$guardo = false;
						}
					}
					//$newResponse = $newResponse->withAddedHeader('alertType', "success");
					if ($guardo) {
						if ($miUsuario->ModificarUsuario()>0) {
							$rta = "Usuario modificado";
						} else {
							$rta = "No se modificó el usuario";
						}
					}
				}					
			}	
		}	

		$newResponse->getBody()->write($rta);

        return $newResponse;
    }

}
?>