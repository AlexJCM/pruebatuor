<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model{
    //put your code here
    protected $table = 'imagen';
    protected  $primaryKey = 'imagen_id'; 
    protected  $fillable = ['descripcion','ruta'];
    protected $guarded= ['sitioTuristico_id','imagen_id'];
    //este modelo tiene created_at y por ende va obligatoriamente el updated_at
    
    public function sitioTuristico() {
        return $this->belongsTo('App\Models\SitioTuristico', 'sitioTuristico_id');
   //belongsTo va en la entidad debil respecto a la fuerte
        }
    
}
