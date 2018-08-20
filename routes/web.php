<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puede registrar todas las rutas para una aplicación.
    Es una brisa. Simplemente dile a Lumen los URI a los que debería responder
y darle el Closure para llamar cuando se solicita ese URI.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/*el navegador chrome solo admite  GET 
POR LO QUE UTILIZAREMOS POSTMAN ,ahí hay que seleccionar Body-raw-JSON y enviar
los campos con sus respectivos datos en formato JSON para editar un registro.
 * get se utiliza más para  listar o buscar*/

/**
 * Solo algunas funciones de ControladorVisita y ControladorUsuario requieren autenticación.
 * para autentificarse Y tener acceso a algunas funciones hay que ir al Postamn en modo post y luego a HEADERS, posteriormente
 * hay que escribir en KEY el nombre de nuestra api-token(en este caso Api-Token) y en VALUE se pone el token que
 * obtuvimos al loguearnos
 */

////EndPoints de USUARIO
$router->get('/usuario/listar', 'ControladorUsuario@listarUsuarios');//
$router->post('/usuario/modificar/{external_id}', 'ControladorUsuario@modificarUsuario');
$router->post('/usuario/login', 'ControladorUsuario@inicioSesion');
/*seleccionar Body-raw-JSON y enviar el correo y clave en formato json 
 * para loguearse, y como extra obtendremos el token
 */
$router->post('/usuario/buscar/{external_id}', 'ControladorUsuario@buscarUsuario');
$router->post('/usuario/registrar', 'ControladorUsuario@registrarUsuario');


////EndPoints de SitioTuristico
$router->get('/sitio/listar', 'ControladorSitioTuristico@listarSitios');
$router->get('/sitio/buscar/{cadena}', 'ControladorSitioTuristico@buscarSitio');
$router->get('/sitio/buscarDos/{search}', 'ControladorSitioTuristico@buscarSitioDos');
$router->post('/sitio/eliminar/{external_id}', 'ControladorSitioTuristico@eliminarSitio');
$router->post('/sitio/editar/{external_id}', 'ControladorSitioTuristico@modificarSitio');
$router->post('/sitio/registrar', 'ControladorSitioTuristico@registrarSitio');

////EndPoints de Visita
$router->get('/visita/listar','ControladorVisita@listarVisita');
$router->get('/visita/listarVisitasUsuario/{external_id}','ControladorVisita@listarVisitaUsuario');
$router->get('/visita/listarMasVisitados','ControladorVisita@listarSitiosMasVisitados');
$router->post('/visita/registrar', 'ControladorVisita@registrarVisita');//
$router->post('/visita/editarLike/{idVisita}', 'ControladorVisita@editarLikeVisita');
$router->post('/visita/editarFavorito/{idVisita}', 'ControladorVisita@editarFavoritoVisita');
$router->get('/visita/listarFavoritos/{external_id}', 'ControladorVisita@listarFavoritosUsuario');
//Test
$router->get('/listarVisitasTwo','ControladorVisita@testPluck');
$router->get('/listarVisitasThree','ControladorVisita@testPluck2');
$router->get('/testMax','ControladorVisita@testMax');
$router->get('/testJoin','ControladorVisita@testJoin');
$router->get('/testConsultas','ControladorVisita@testConsultas');
$router->get('/testGroupBy','ControladorVisita@testGroupBy');

////EndPoints de Imagen
$router->get('/imagen/listar','ControladorImagen@listarImagen');
$router->post('/imagen/eliminar/{imagen_id}', 'ControladorImagen@eliminarImagen');
$router->post('/imagen/registrar', 'ControladorImagen@guardarImagen');//
$router->post('/imagen/modificar/{imagen_id}', 'ControladorImagen@modificarImagen');
