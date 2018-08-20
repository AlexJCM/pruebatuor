<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visita;
use App\Models\Usuario;
use App\Models\SitioTuristico;

class ControladorVisita extends Controller {
     //CREAR VARIABLES GLOBALES PARA LISTARVisitaUsuario
     private $external_id;
     
      public function __construct() {       
        $this->middleware('auth', ['only' =>
            [
                'listarVisitaUsuario',
                'listarFavoritosUsuario',
                'registrarVisita',
                'editarLikeVisita',
                'editarFavoritoVisita'
            ]
        ]);
    }
    
    /**
     * Función que lista todas las visitas en general
     * @return type
     */
    public function listarVisita() {
        $lista = Visita::orderBy('fecha_visita', 'desc')->get();
        /**
         * La diferencia entre get() y first() es que en el primero obtienes un array y en la segunda obtienes un objeto con un único elemento. Para comprobarlo, te damos dos pedazos de código realizarán la misma tarea. Solo difiere la manera de acceder al primer elemento para recuperar su nombre.
// Si uso first() accedo a las columnas del registro mediante un objeto
$libro = DB::table('books')->first();
echo $libro->name;
// si uso get() recibo un array, cuyo primer registro se indexa con [0]
$libros = DB::table('books')->get();
echo $libros[0]->name;
         */
        
        $data = array();
        foreach ($lista as $item) {
            $data[] = ["IdSitioTuristico"=>$item->sitioTuristico_id,
                "fechaDeVisita" => $item->fecha_visita,
                "favorito" => $item->favorito,
                "Likes" => $item->me_gusta];
        }
        return response()->json($data, 200);
        /* crear una carpeta client, dentro de ella index.html y en ella crear la lista y el registro de noticias
          con jquery, ajax */
    }
    
    /**
     * Esta funcion lista las visitas de un usuario en especifico
     * @param type $external_id
     * @return type
     */
     public function listarVisitaUsuario($external_id) {//vamos a recibir el external_id del usuario
        $this->external_id = $external_id;
        
        $lista = Visita::whereHas('Usuario', function($q) {
                    $q->where('external_id', $this->external_id);
                })->orderBy('fecha_visita', 'desc')->get();


        $data = array();
        foreach ($lista as $item) {
            $data[] = ["idVisita" => $item->visita_id,"fechaViisita" => $item->fecha_visita,
                "IDsiTioTuristicooo" => $item->sitioTuristico_id,
                "usuario" => $item->id_usuario];
        }

        return response()->json($data, 200);
    }
           
    /**
     * Este método lista los 3 sitios más visitados
     * @return type array
     */
    public function listarSitiosMasVisitados() {
        //Obtiene el resultado contado y relacionando dos tablas
        $lista = Visita::join('sitioTuristico', function ($join) {
                            $join->on('visita.sitioTuristico_id', '=', 'sitioTuristico.sitioTuristico_id')
                            ->where('visita.favorito', '<>', 1);
                        })->selectRaw('visita.id_usuario, visita.sitioTuristico_id, count(*) as contador')
                        ->groupBy('sitioTuristico_id')
                        ->orderBy('contador', 'desc')
                        ->take(3)->get();

        //Obtiene el resultado solo contando los valores en la misma tabla
        $listaB = Visita::selectRaw('sitioTuristico_id, count(*) as contador')
                        ->groupBy('sitioTuristico_id')
                        ->orderBy('contador', 'desc')
                        ->take(3)->get();

        return response()->json($lista, 200);
    }
    
    /**
     * Función privada para obtener el nombre de un sitio turistico mediante su id
     * @param type $sitio_id
     * @return type String
     */
    private function buscarSitio($sitio_id) {
        $sitioObjeto = SitioTuristico::where('sitioTuristico_id', $sitio_id)->first();
        $nombreS = $sitioObjeto->nombre;

        return $nombreS;
    }
    
    /**
     * Función que recibe el extenal_id de un usuario para presentar sus sitios favoritos
     * @param type $external_id
     * @return type array
     */
    public function listarFavoritosUsuario($external_id) {
         $this->external_id = $external_id;
        
        $lista = Visita::whereHas('Usuario', function($q) {
                    $q->where('external_id', $this->external_id);
                })->where('favorito',1) ->orderBy('fecha_visita', 'desc') ->get();

        $data = array();
        foreach ($lista as $item) {
            $sitio_id = $item->sitioTuristico_id;
            $nombreS = ControladorVisita::buscarSitio($sitio_id);
            $data[] = ["idVisita" => $item->visita_id, "fechaVisita" => $item->fecha_visita,
                "iDsiTioTuristicooo" => $item->sitioTuristico_id,
                "usuario" => $item->id_usuario,
                "nombreSitio" => $nombreS];
        }

        return response()->json($data, 200);
    }

    /**
     * Esta función permite guerdar una visita por parte del usuario automaticamente
     * @param Request $request
     * @return type mensaje
     * @author John Doe <john.doe@example.com>
     * @version string
     */
    public function registrarVisita(Request $request) {
        if ($request->isJson()) {
            $data = $request->json()->all();

            try {              
                $user = Usuario::where('external_id', $data["external"])->first(); //data[] viene desde la vista
                $site = SitioTuristico::where('external_id', $data['externalSitio'])->first();

                if ($user && $site) {
                    $usuario = Usuario::find($user->id_usuario);
                    $sitio = SitioTuristico::find($site->sitioTuristico_id);

                    $visita = new Visita();
                    
                    $visita->fecha_visita = date('Y-m-d H:i:s');
                    $visita->favorito = 0;
                    $visita->me_gusta = 0; 
                    $visita->visitado = 1;                    
                    $visita->usuario()->associate($user); //
                    $visita->sitioTuristico()->associate($site);//                  

                    $visita->save();

                    return response()->json(["mensaje" => "Operacion existosa al registrar visita", "siglas" => "OE"], 200)->header('Access-Control-Allow-Origin', '*');
                } else {

                    return response()->json(["mensaje" => "No se encontraron datos en Visitas y /o Usuarios", "siglas" => "NDE"], 203);
                }
            } catch (\Exception $exc) {//se le pone backslash en el catch
                    return response()->json(["mensaje" => "try catch.  Faltan datos", "siglas" => "Fd"], 400);
            }
        } else {
            return response()->json(["mensaje" => "La data no tiene el formato deseado", "siglas" => "DNF"], 400);
        }
    }
     
    /**
      * Función para dar like o dislike a un sitio turistico durante la visita
      * @param type $idVisita
      * @return type mensaje
      * @author John Doe <john.doe@example.com>
      */
    public function editarLikeVisita($idVisita) {                 
            try {
                $visitaObjeto = Visita::where('visita_id', $idVisita)->first();                
               
                if ($visitaObjeto) {                                     
                   if ($visitaObjeto->me_gusta == 0) {                       
                        echo 'Has dado Like';
                        $visitaObjeto->me_gusta = 1;
                        $visitaObjeto->save();
                        
                    } else {
                        echo 'Has dado Dislike';
                        $visitaObjeto->me_gusta = 0;
                        $visitaObjeto->save();
                    }
                    return response()->json(["mensaje" => "Operacion existosa al editar Like de visita", "siglas" => "OE"], 200)->header('Access-Control-Allow-Origin', '*');
                } else {

                    return response()->json(["mensaje" => "No se encontraron datos en Visitas", "siglas" => "NDE"], 203);
                }
            } catch (\Exception $exc) {                
                return response()->json(["mensaje" => "Try catch.  Faltan datos y "+$exc, "siglas" => "FD"], 400);
            }
        
    }
    
    /**
     * Método que permite cambiar el estado de favorito en una visita
     * @param type $idVisita
     * @return type mensaje
     */
    public function editarFavoritoVisita($idVisita) {
         try {
                $visitaObjeto = Visita::where('visita_id', $idVisita)->first();                
               
                if ($visitaObjeto) {                                     
                   if ($visitaObjeto->favorito == 0) {                       
                        echo 'Ahora este sitio esta entre tus Favoritos ';
                        $visitaObjeto->favorito = 1;
                        $visitaObjeto->save();
                        
                    } else {
                        echo 'Has quitado a este sitio de tus favoritos ';
                        $visitaObjeto->favorito = 0;
                        $visitaObjeto->save();
                    }
                    return response()->json(["mensaje" => "Operacion existosa al editar Favorito de visita", "siglas" => "OE"], 200)->header('Access-Control-Allow-Origin', '*');
                } else {
                    return response()->json(["mensaje" => "No se encontraron datos en Visitas", "siglas" => "NDE"], 203);
                }
            } catch (\Exception $exc) {                
                return response()->json(["mensaje" => "Try catch.  Faltan datos Y "+$exc, "siglas" => "FD"], 400);
            }
        
    }

     /**
     * //TEST de consultas. Ingorar este codigo
     * @return type array
     */
    public function testPluck() {       // 
        $lista = Usuario::whereHas('visita')->pluck('nombre', 'clave');
        //al parecer pluck no obtiene mas de dos campos
        return response()->json($lista, 200);
    }

    public function testPluck2() {       // 
        $lista = Usuario::doesntHave('visita')->pluck('nombre', 'clave');
        //al parecer pluck no obtiene mas de dos campos
        return response()->json($lista, 200);
    }

    public function testMax() {
        $lista = \App\Models\SitioTuristico::max('altura');
        return response()->json($lista, 200);
    }   
 
    public function testConsultas() {
        $lista = \App\Models\Usuario::join('visita', function ($join) {
                    $join->on('usuario.id_usuario', '=', 'visita.id_usuario')
                    ->where('visita.favorito', '<>', 0);
                })
                ->get();
        return response()->json($lista, 200);
       
    }

    public function testJoin() {
        //en este tipo de consultas cuando se envia en el select dos parametros con el mismo nombre
        //solo presentará el segundo parametro por lo que hay que usar la sentencia 'as'
        $lista = \App\Models\Usuario::join('visita', 'usuario.id_usuario', '=', 'visita.id_usuario')
                ->join('sitioTuristico', 'visita.sitioTuristico_id', '=', 'sitioTuristico.sitioTuristico_id')
                ->select('usuario.nombre as name', 'sitioTuristico.nombre', 'visita.fecha_visita')
                ->get();

        return response()->json($lista, 200);
    }

    public function testGroupBy() {
        $lista = \App\Models\Usuario::groupBy('edad')
                ->having('id_usuario', '>', 2)
                ->get();
        //para obtener la consulta en crudo hay que cambiar el get por toSql()
        //para que funcione el groupBy hay que poner esta linea 'strict' => false en database.php
        return response()->json($lista, 200);
    }

}
