<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SitioTuristico extends Model{
    //put your code here
    protected $table = 'sitioTuristico';
    protected  $primaryKey = 'sitioTuristico_id';
    public $timestamps =false;
    protected  $fillable = ['nombre','descripcion','tipo','telefono',
                            'direccion','horarios','sitioWeb',
                            'climatologia','altura','latitud','longuitud'
                          ];
    protected $guarded= ['sitioTuristico_id','external_id'];
    
    
    public function imagen() {
        return $this->hasMany('App\Models\Imagen', 'sitioTuristico_id');
        
    }
    
    public function visita() {
        return $this->hasMany('App\Models\Visita', 'sitioTuristico_id');
    }
    
    
}
