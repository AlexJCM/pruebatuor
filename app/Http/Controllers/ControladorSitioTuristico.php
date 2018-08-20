<?php

namespace App\Http\Controllers;

use App\Models\SitioTuristico;
use Illuminate\Http\Request;

/**
 * 
 */
class ControladorSitioTuristico extends Controller {

    //falta agregar los constructores a cada controlador a autorizar
    //CREAR VARIABLES GLOBALES PARA LISTARNOTICIASADMINISTRADOR
    private $external_id;
      

    /**
     * Función que lista  todos los sitio turísticos registrados
     * @return type array
     */
    public function listarSitios() {
        $lista = SitioTuristico::orderBy('nombre', 'desc')->get();
        $data = array();
        foreach ($lista as $item) {
            $data[] = ["nombre" => $item->nombre, "desc" => $item->descripcion,
                "tipo" => $item->tipo,
                "height" => $item->altura,
                "external_id" => $item->external_id]; //enviamos el external_id de sitioTuristico para luego pòder editarlo
        }
        return response()->json($data, 200);
        /* crear una carpeta client, dentro de ella index.html y en ella crear la lista y el registro de noticias
          con jquery, ajax */
        //A $data se descompone en formato json en el postman para mostrar su contenido
    }
              
    /**
     * Método para registrar un nuevo sitio turístico al recibir como parámetro un objeto tipo Request
     * @param Request $request
     * @return type mensaje
     */
    public function registrarSitio(Request $request) {
        if ($request->isJson()) {
            $data = $request->json()->all();

            try {
                $sitioT = new SitioTuristico();

                $sitioT->nombre = $data['nombre'];
                $sitioT->descripcion = $data['descripcion'];
                $sitioT->altura = $data['height'];
                $sitioT->latitud = number_format($data['latitud'], 6, '.', ''); //convertir a notacion inglesa antes de guardar sin 
                ////separador de millares y con el . como separador de decimales, el 6 es el numero maximo de decimales
                $sitioT->external_id = Utilidades\UUID::v4();

                $sitioT->save();

                return response()->json(["mensaje" => "Operacion existosa al registrar sitio", "siglas" => "OE"], 200)->header('Access-Control-Allow-Origin', '*');
            } catch (\Exception $exc) {//se le pone backslash en el catch
                //echo $exc->getTraceAsString();
                return response()->json(["mensaje" => "try catch.  Faltan datos", "siglas" => "Fd"], 400);
            }
        } else {
            return response()->json(["mensaje" => "La data no tiene el formato deseado", "siglas" => "DNF"], 400);
        }
    }

    /**
     * Función que recibe 2 parámetros para modificar el registro de un sitio turístico
     * @param Request $request
     * @param type $external_id
     * @return type mensaje
     */
    public function modificarSitio(Request $request, $external_id) {
        $siteObjeto = SitioTuristico::where('external_id', $external_id)->first(); /* obtenemos el objeto(en un arreglo asociado) */

        if ($siteObjeto) {
            if ($request->isJson()) {

                $data = $request->json()->all(); //lo convierte al arreglo a formato json
                $sitio = SitioTuristico::find($siteObjeto->sitioTuristico_id);

                if (isset($data['nombre'])) {
                    $sitio->nombre = $data['nombre'];
                }
                if (isset($data['descripcion'])) {
                    $sitio->descripcion = $data['descripcion'];
                }
                if (isset($data['tipo'])) {
                    $sitio->tipo = $data['tipo'];
                }

                $sitio->save();

                return response()->json(["mensaje" => "Operacion existosa en Editar Sitio", "siglas" => "OE"], 200);
                //todo esto es un ednpoint y se lo regstra en el router
                //es un post
            } else {
                return response()->json(["mensaje" => "La data no tiene el formato deseado", "siglas" => "DNF"], 400);
            }
        } else {
            return response()->json(["mensaje" => "No se encontraron datos de Sitio", "siglas" => "NDE"], 203);
        }
    }

    /**
     * Método que recibe un parametro con el cual se buscará el sitio turístico a eliminarse
     * @param type $external_id
     * @return type
     */
    public function eliminarSitio($external_id) {
        ///no hay como eliminar porque tiene tablas que dependen de esta        
        $siteObjeto = SitioTuristico::where('external_id', $external_id)->first(); /* obtenemos el objeto(en un arreglo asociado) */

        if ($siteObjeto) {

            try {
                $site = SitioTuristico::find($siteObjeto->sitioTuristico_id);

                $imagen = \App\Models\Imagen::where('sitioTuristico_id', $site->sitioTuristico_id)->first(); //esta linea ayuda a ver si sitio 
                ////tiene registros relacionados en otra tabla

                if ($imagen) {
                    return response()->json(["mensaje" => "No se puede eliminar el Sito porque tiene registro relacionados en otras tablas", "siglas" => "OE"], 200);
                } else {
                    $site->delete();
                    return response()->json(["mensaje" => "Operacion existosa en eliminar Sitio", "siglas" => "OE"], 200);
                }
                //todo esto es un ednpoint y se lo regstra en el router
                //es un post
            } catch (Exception $exc) {
                return response()->json(["mensaje" => "try catch.  No se pudo eliminar", "siglas" => "Fd"], 500);
            }
        } else {
            return response()->json(["mensaje" => "No se encontraron datos del Sitio", "siglas" => "NDE"], 203);
        }
    }

    /**
     * Método para buscar mediante un parámetro que recibimos desde la máscara de la url
     * y únicamente busca por el campo nombre en la tabla sitioTuristico
     * @version string
     * @param type $cadena
     * @return type array
     */
    public function buscarSitio($cadena) {

        $cadenaEdit = "%" . $cadena . "%"; //recibimos la cadena a buscar desde la mascara de la url

        $siteObjeto = SitioTuristico::where('nombre', 'like', $cadenaEdit)->get();

        $data = array();

        foreach ($siteObjeto as $value) {
            $data[] = ["descripcion" => $value->descripcion,
                "nombre" => $value->nombre
            ];
        }
        return response()->json($data, 200);
    }

    /**
     * Método para buscar mediante un parámetro que recibimos desde la máscara de la url
     * y busca por el campo nombre y/o por el campo descripcion en la tabla sitioTuristico
     * @version string
     * @param type $cadena
     * @return type array
     */
    public function buscarSitioDos($search) {
        $sites = SitioTuristico::from('sitioTuristico as s')
                ->where(function ($query) use ($search) {
            $query = $query->orWhere('s.nombre', 'like', "%$search%");
            $query = $query->orWhere('s.descripcion', 'like', "%$search%");
        });

        $sites = $sites->get(); //sites es un array multidimensional       

        foreach ($sites as $item) {
            $data[] = $item->nombre . ' - ' . $item->descripcion . ', <br>';
        }

        return response()->json($data, 200);
    }

     
    
    
        }
