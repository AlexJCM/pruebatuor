<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model{
    //put your code here
    protected $table = 'visita';
    protected  $primaryKey = 'sitioTuristico_id';
    public $timestamps =false;
    protected  $fillable = ['fecha_visita','favorito','visitado','me_gusta'];
    protected $guarded= ['sitioTuristico_id','id_usuario'];
    
  
    public function usuario() {
        return $this->belongsTo('App\Models\Usuario', 'id_usuario');
    }
    
    public function sitioTuristico() {
        return $this->belongsTo('App\Models\SitioTuristico', 'sitioTuristico_id');
    }
}
