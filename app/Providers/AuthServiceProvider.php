<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Esta funcion arranca los servicios de autenticación para la aplicación
     *
     * @return void
     */
    public function boot()
    {        
        /**
         * Aquí puede definir cómo desea que los usuarios se autentiquen para su aplicación Lumen
         * La devolución de llamada que recibe la instancia de solicitud entrante debería devolver una instancia de usuario o null. 
         * Usted es libre de obtener la instancia del Usuario a través de un token API o cualquier otro método necesario.
         */

       
         $this->app['auth']->viaRequest('api', function ($request) {
           // if ($request->input('api_token')) {
             //   return User::where('api_token', $request->input('api_token'))->first();
            //}
            $user = null;
            $header = $request->header('Api-Token');
            
            if ($header) {
                $data = base64_decode($header); //decode porque está llegando(). Le voy a enviar external_id--correo
                $arreglo = explode("--", $data); //es similar al split de java

                if (count($arreglo) == 2) {//Va 2 porque estamos usando external_id y usuario
                    $userAux = \App\Models\Usuario::where('external_id', $arreglo[0])->where('correo', $arreglo[1])->first();

                    if ($userAux) {
                        $user = $userAux;
                    }
                }
            }

            return $user;
        });
    }
}
