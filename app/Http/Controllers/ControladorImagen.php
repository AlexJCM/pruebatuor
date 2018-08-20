<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Imagen;

class ControladorImagen extends Controller {

    //put your code here
    //Listar todas las imagenes
    public function listarImagen() {
        $lista = Imagen::orderBy('created_at', 'desc')->get();
        $data = array();
        foreach ($lista as $item) {
            $data[] = ["desc" => $item->descripcion,
                "fecha" => $item->created_at->format('Y-m-d'),
                "ruta" =>$item->ruta];//
        }
        return response()->json($data, 200);
      
    }
    
    /**
     * Este método permite guardar una imagen utilizando el id qdel sitio que nos envien en la data
     * @param Request $request
     * @return type mensaje
     */
    public function guardarImagen(Request $request) {   
        if ($request->isJson()) {
            $data = $request->json()->all();

            try {
                $imagen = new Imagen();
                
                $imagen->descripcion = $data['descripcion'];
                $imagen->ruta = $data['ruta'];         
                $imagen->sitioTuristico_id = $data['sitio_id'];                
                $imagen->created_at = date('Y-m-d H:i:s');           
                                
                $imagen->save();

                return response()->json(["mensaje" => "Operacion existosa al guardar imagen", "siglas" => "OE"], 200)->header('Access-Control-Allow-Origin', '*');
               
            } catch (\Exception $exc) {                
                return response()->json(["mensaje" => "Try catch.  Faltan datos y "+$exc, "siglas" => "FD"], 400);
            }
            
        } else {
            return response()->json(["mensaje" => "La data no tiene el formato deseado", "siglas" => "DNF"], 400);
        }
    }
    
    /**
     * Función que permite editr una imagen mediante su id y la data con sus respectivos valores que nos envien desde la vista
     * @param Request $request
     * @param type $imagen_id
     * @return type mensaje
     */
    public function modificarImagen(Request $request, $imagen_id) {
        $imageObjeto = Imagen::where('imagen_id',$imagen_id)->first();/*obtenemos el objeto(en un arreglo asociado)*/
        
        if ($imageObjeto) {
            
            if ($request->isJson()) {               

                $data = $request->json()->all();//lo convierte al arreglo a formato json
                
                 try {
                     $imagen = Imagen::find($imageObjeto->imagen_id);                
              //van con el isset por si acaso editen un solo campo 
                if (isset($data['descripcion'])) {
                    $imagen->descripcion = $data['descripcion'];
                } 
                  if (isset($data['ruta'])) {
                    $imagen->ruta = $data['ruta'];
                }
                if (isset($data['sitio_id'])) {
                    $imagen->sitioTuristico_id = $data['sitio_id'];
                }                           
           
               //no hace falta  editar el updated_at ya que lumen nos ayuda con es prte al modificar un registro                
                
                $imagen->save();
                
                return response()->json(["mensaje" => "Operacion existosa en Editar Imagen","siglas"=>"OE"], 200);
                //todo esto es un ednpoint y se lo regstra en el router
                //es un post
                } catch (\Exception $exc) {                    
                     return response()->json(["mensaje" => "Try catch.  Faltan datos y "+$exc, "siglas" => "FD"], 400);
                }
               
            }
            else{
                return response()->json(["mensaje" => "La data no tiene el formato deseado","siglas"=>"DNF"], 400);
            }
        } 
        else{
             return response()->json(["mensaje" => "No se encontraron datos de Imagen","siglas"=>"NDE"], 203);
        }
    }
    
    /**
     * Este método elimina una imagen mediante su id
     * @param type $imagen_id
     * @return type mensaje
     */
    public function eliminarImagen($imagen_id) {     
        
        $imageObjeto = Imagen::where('imagen_id', $imagen_id)->first(); /* obtenemos el objeto(en un arreglo asociado) */
      
        if ($imageObjeto) {
                       
            try {
                $imagen = Imagen::find($imageObjeto->imagen_id);           
                                           
                $imagen->delete();
                
                return response()->json(["mensaje" => "Operacion existosa en eliminar la imagen", "siglas" => "OE"], 200);
                                
                //todo esto es un ednpoint y se lo regstra en el router
                //es un post
            } catch (Exception $exc) {               
                return response()->json(["mensaje" => "try catch.  No se pudo eliminar la Imagen", "siglas" => "Fd"], 500);
            }
            
        } else {
            return response()->json(["mensaje" => "No se encontraron datos de la Imagen", "siglas" => "NDE"], 203);
        }
    }
   
    
}
