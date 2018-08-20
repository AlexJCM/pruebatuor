<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model{
    //put your code here
    protected $table = 'usuario';
    protected  $primaryKey = 'id_usuario';
    public $timestamps =false;
    protected  $fillable = ['nombre','genero','edad', 'correo'];
    protected $guarded= ['external_id','clave','id_usuario'];
    
    /**
     * 
     * @return type
     */
    public function visita() {
        return $this->hasMany('App\Models\Visita','id_usuario');
    }
    
    
}
