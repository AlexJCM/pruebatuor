<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class ControladorUsuario extends Controller {
    
     public function __construct() {       
        $this->middleware('auth', ['only' =>
            [
                'modificarUsuario'
            ]
        ]);
    }
 
    /**
     * Función que lista todos los usuarios registrados
     * @return type array
     */
     public function listarUsuarios(){
        $lista = Usuario::all();
       $data =array();
       foreach ($lista as $item) {
           $data[]=["nombre"=>$item->nombre, "correo"=>$item->correo];
           
       }
       return response()->json($data,200);//retorna data para ser usado por restAdministrado.js
    }
    
    /**
     * Esta función permite buscar un usuario mediante su external_id
     * @param type $external_id
     * @return type array
     */
    public function buscarUsuario($external_id){
        $lista = Usuario::where('external_id', $external_id)->first();
       
       return response()->json($lista,200);
    }
    
    /**
     * Esta función permite registrar un usuario al recibir una data desde la vista
     * @param Request $request
     * @return type mensaje
     */
    public function registrarUsuario(Request $request) {
         if ($request->isJson()) {
            $data = $request->json()->all();

            try {
                $usuario = new Usuario();

                $usuario->nombre = $data['nombre'];
                $usuario->correo = $data['correo'];
                $usuario->clave = $data['clave'];                             
                $usuario->genero = $data['genero'];   
                if (!isset($data['edad'])) 
                    $usuario->edad = 0;   
                else
                    $usuario->edad = $data['edad'];   
                $usuario->external_id = Utilidades\UUID::v4();

                $usuario->save();

                return response()->json(["mensaje" => "Operacion existosa al registrar el Usuario", "siglas" => "OE"], 200)->header('Access-Control-Allow-Origin', '*');
            } catch (\Exception $exc) {                
                return response()->json(["mensaje" => "try catch.  Faltan datos y "+$exc, "siglas" => "FD"], 400);
            }
        } else {
            return response()->json(["mensaje" => "La data no tiene el formato deseado", "siglas" => "DNF"], 400);
        }
    }
    
    /**
     * Este método permite editar el registro de un usuario mediante su external_id
     * @param Request $request
     * @param type $external_id
     * @return type mensaje
     */
    public function modificarUsuario(Request $request, $external_id){//request sirve para obtener los datos 
    //de la peticion
        $userObjeto = Usuario::where('external_id',$external_id)->first();/*obtnemos el objeto(en un arreglo asociado)*/
        
        if ($userObjeto) {
            if ($request->isJson()) {
                
                $data = $request->json()->all();//lo convierte al arreglo a formato json
                $user =Usuario::find($userObjeto->id_usuario);
                
                if (isset($data['nombre']))                    
                         $user->nombre = $data['nombre'];
                
                if (isset($data['clave']))  
                        $user->clave=$data['clave'];
            
                if (isset($data['correo']))  
                    $user->correo=$data['correo'];
                
                $user->save();
                
                return response()->json(["mensaje" => "Operacion existosa","siglas"=>"OE"], 200);
                //todo esto es un ednpoint y se lo regstra en el router
                //es un post
            }
            else{
                return response()->json(["mensaje" => "La data no tiene el formato deseado","siglas"=>"DNF"], 400);
            }
        } 
        else{
             return response()->json(["mensaje" => "No se encontraron datos en Usuario","siglas"=>"NDE"], 203);
        }
    }
    
    
    /**
     * Esta función permite iniciar sesión al usuario y codificar un token el cual será descodificado en la funcion
     * boot de AuthServiceProvider
     * @param Request $request
     * @return type array
     */
    public function inicioSesion(Request $request) {
        if ($request->isJson()) {//
            try {
                $data = $request->json()->all();
                $usuario = Usuario::where('correo', $data['correo'])->where('clave', $data['clave'])->first();
                //get para toda la lista, first para el primero, last para obtenerel ultimo
                
                if ($usuario){
                    return response()->json(["nombre"=>$usuario->nombre,
                        "external_id"=>$usuario->external_id,                        
                        "token"=> base64_encode($usuario->external_id.'--'.$usuario->correo),//codifica el token                        
                        "mensaje" => "Inicio de sision existosa","siglas"=>"OE"], 200);
                    
                }   else
                    {
                     return response()->json(["mensaje" => "No se encontraron datos en usuario","siglas"=>"NDE"], 203);
                }             
            } catch (\Exception $exc) {
               
                return response()->json(["mensaje" => "Faltan datos en inicioSesion","siglas"=>"NDE"], 400);
            }
       }    
      else 
            {
          return response()->json(["mensaje" => "La data no tiene el formato deseado", "siglas" => "DNF"], 400);
        }
    }
   
}
