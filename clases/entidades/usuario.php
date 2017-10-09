<?php
class usuario
{
	public $id_usuario;//PK
 	public $nombre;
  	public $apellido;
  	public $mail;//Unique
	public $username;//Unique
	private $password;
	public $habilitado;
	public $foto;

  	public function BorrarUsuario(){
		  $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		  $consulta =$objetoAccesoDato->RetornarConsulta("DELETE from usuarios WHERE id_usuario=:id_usuario");
		  $consulta->bindValue(':id_usuario',$this->id_usuario, PDO::PARAM_INT);
		  $consulta->execute();
		  
		  return $consulta->rowCount();
	}

	public static function BorrarUsuarioPorUsername($username){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("DELETE from usuarios WHERE username=:username");	
		$consulta->bindValue(':username',$username, PDO::PARAM_STR);		
		$consulta->execute();
		
		return $consulta->rowCount();
	}
	 
	public function ModificarUsuario(){
		 $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		 //modifica enlazando parametros de la instancia
		 $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE usuarios SET 
		 nombre=:nombre,
		 apellido=:apellido,
		 mail=:mail,
		 username=:username,
		 password=:password, 
		 habilitado=:habilitado,
		 foto=:foto		 
		 
		 WHERE id_usuario=:id_usuario");
		 
		 $consulta->bindValue(':id_usuario',$this->id_usuario, PDO::PARAM_INT);
		 $consulta->bindValue(':nombre',$this->nombre, PDO::PARAM_STR);
		 $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
		 $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
		 $consulta->bindValue(':username', $this->username, PDO::PARAM_STR);
		 $consulta->bindValue(':password', $this->password, PDO::PARAM_STR);
		 $consulta->bindValue(':habilitado', $this->habilitado, PDO::PARAM_INT);
		 $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
		 
		 return $consulta->execute();
	}
	 
	public function InsertarUsuario(){
		 $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		 //inserta enlazando parametros dela instancia
		 $consulta =$objetoAccesoDato->RetornarConsulta("INSERT INTO usuarios (nombre,apellido,mail,username,password,habilitado,foto)
		 values(:nombre,:apellido,:mail,:username,:password,:habilitado,:foto)");

		 $consulta->bindValue(':nombre',$this->nombre, PDO::PARAM_STR);
		 $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
		 $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
		 $consulta->bindValue(':username', $this->username, PDO::PARAM_STR);
		 $consulta->bindValue(':password', $this->password, PDO::PARAM_STR);
		 $consulta->bindValue(':habilitado', $this->habilitado, PDO::PARAM_INT);
		 $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
		 
		 return $consulta->execute(); 
	}
	
	public function GuardarUsuario(){
        if(empty(Usuario::TraerUnUsuario($this->username))){
            $this->InsertarUsuario();
            echo "Usuario guardado";
        } else {
			//Si sólo está ID como unique no hace falta traerlo
            $elUsuario = Usuario::TraerUnUsuario($this->username);
            $this->id_usuario = $elUsuario->id_usuario;
            
            if ($this->ModificarUsuario()) {
                echo "Usuario modificado";
            } else {
                echo "No modifico Usuario";
            }
        }
	}
	 
	public static function TraerTodosLosUsuarios(){
		 $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
		 $consulta =$objetoAccesoDato->RetornarConsulta("SELECT id_usuario, nombre, apellido, mail, username, habilitado, foto FROM usuarios");
		 $consulta->execute();
		 
		 return $consulta->fetchAll(PDO::FETCH_CLASS, "Usuario");		
	}

	public static function TraerUnUsuario($username){
		//trae por algún UNIQUE
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("SELECT id_usuario, nombre, apellido, mail, username, habilitado, foto FROM usuarios where username = :username");
		$consulta->bindValue(':username',$username, PDO::PARAM_STR);
        $consulta->execute();
		$UsuarioBuscado= $consulta->fetchObject('Usuario');

		return $UsuarioBuscado;
    }

	public static function TraerUnUsuarioPorId($id_usuario){
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("SELECT id_usuario, nombre, apellido, mail, username, habilitado FROM usuarios where id_usuario = :id_usuario");
		$consulta->bindValue(':id_usuario',$id_usuario, PDO::PARAM_INT);
		$usuarioBuscado= $consulta->fetchObject('usuario');
		
		return $usuarioBuscado;
	}

	public static function BorrarUsuarioPorParametro($username){
		//Borra por UNIQUE sino tendría que ser por ID
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("DELETE FROM usuarios WHERE username=:username");	
		$consulta->bindValue(':username',$username, PDO::PARAM_STR);		
		$consulta->execute();

		return $consulta->rowCount();
    }

	public function mostrarDatos(){
	  	return "Metodo mostar:".$this->nombre."  ".$this->apellido."  ".$this->mail."  ".$this->username."  ".$this->habilitado;
	}

	public function setPassword($pass){
		$this->password = $pass;
	}

}