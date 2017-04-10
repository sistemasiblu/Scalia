<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Embarque extends Model
{
    protected $table = 'embarque';
    protected $primaryKey = 'idEmbarque';

    protected $fillable = ['numeroEmbarque','sufijoEmbarque','fechaElaboracionEmbarque','tipoTransporteEmbarque','TipoTransporte_idTipoTransporte','puertoCargaEmbarque','Ciudad_idPuerto_Carga','puertoDescargaEmbarque','Ciudad_idPuerto_Descarga','Tercero_idTransportador', 'agenteCargaEmbarque', 'Tercero_idAgenteCarga', 'navieraEmbarque', 'Tercero_idNaviera', 'fechaRealEmbarque', 'bodegaEmbarque', 'bodegaCorreoEmbarque', 'otmEmbarque', 'otmCorreoEmbarque', 'volumenTotalEmbarque', 'valorTotalEmbarque', 'unidadTotalEmbarque', 'pesoTotalEmbarque', 'bultoTotalEmbarque', 'DocumentoImportacion_idDocumentoImportacion'];

    public $timestamps = false;

    public function embarquedetalle() 
	{
		return $this->hasMany('App\EmbarqueDetalle','Embarque_idEmbarque');
	}

    public function documentoimportacion()
    {
    	return $this->hasOne('App\DocumentoImportacion','idDocumentoImportacion');
    }
}
