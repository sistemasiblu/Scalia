<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class RolOpcion extends Model
{
    protected $table = 'rolopcion';
    protected $primaryKey = 'idRolOpcion';
    protected $fillable = ['Rol_idRol', 'Opcion_idOpcion', 'adicionarRolOpcion','modificarRolOpcion','eliminarRolOpcion','consultarRolOpcion'];
    public $timestamps = false;
    
    public function rol()
    {
    	return $this->hasOne('App\Rol','idRol');
    }
}