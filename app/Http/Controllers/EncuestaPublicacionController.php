<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use DB;
use Carbon;
use Mail;
use Input;
include public_path().'/ajax/consultarPermisos.php';


class EncuestaPublicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"]);
        $datos = consultarPermisos($vista);

        return view('encuestapublicaciongrid', compact('datos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $encuesta = \App\Encuesta::where('Compania_idCompania','=',\Session::get("idCompania"))->lists('tituloEncuesta', 'idEncuesta');
        
        return view('encuestapublicacion', compact('encuesta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fechahora = Carbon\Carbon::now();

        // Insertamos el encabezado
        \App\EncuestaPublicacion::create([
            'nombreEncuestaPublicacion' => $request['nombreEncuestaPublicacion'],
            'fechaEncuestaPublicacion' => $request['fechaEncuestaPublicacion'],
            'Encuesta_idEncuesta' => $request['Encuesta_idEncuesta'],
            'Users_idCrea' => \Session::get('idUsuario'),
            'created_at' => $fechahora
            ]);

        // Consultamos el ultimo id insertado
        $encuestapublicacion = \App\EncuestaPublicacion::All()->last();
        
        // ejecutamos la funcion para grabar las preguntas y sus opciones
        $this->grabarDetalle($encuestapublicacion->idEncuestaPublicacion, $request);


        return redirect('/encuestapublicacion');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(isset($_GET['accion']) and $_GET['accion'] == 'imprimir')
        {
            return view('formatos.formatoencuesta', compact('id'));
        }

        if(isset($_GET['accion']) and $_GET['accion'] == 'dashboard')
        {
            
            $idEncuestaPublicacion = $_GET['idEncuestaPublicacion'];

            return view('dashboardencuesta',compact('idEncuestaPublicacion'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $encuestapublicacion = \App\EncuestaPublicacion::find($id);

        $encuesta = \App\Encuesta::where('Compania_idCompania','=',\Session::get("idCompania"))->lists('tituloEncuesta', 'idEncuesta');

        return view('encuestapublicacion',['encuestapublicacion'=>$encuestapublicacion],compact('encuesta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $fechahora = Carbon\Carbon::now();

        $encuesta = \App\EncuestaPublicacion::find($id);
        $encuesta->fill($request->all());
        $encuesta->updated_at = $fechahora;
        $encuesta->Users_idModifica = \Session::get('idUsuario');
        $encuesta->save();

        // ejecutamos la funcion para grabar las preguntas y sus opciones
        $this->grabarDetalle($id, $request);


        return redirect('/encuestapublicacion');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\EncuestaPublicacion::destroy($id);
        return redirect('/encuestapublicacion');
    }

    protected function grabarDetalle($id, $request)
    {

        // en el formulario hay un campo oculto en el que almacenamos los 
        // id que se eliminan separados por coma, en este proceso lo convertimos 
        // en array y eliminamos dichos id de la tabla de detalle preguntas
        $idsEliminar = explode(',', $request['eliminarDestino']);
        \App\EncuestaPublicacionDestino::whereIn('idEncuestaPublicacionDestino',$idsEliminar)->delete();

        for($i = 0; $i < count($request['idEncuestaPublicacionDestino']); $i++)
        {
           
            $indice = array(
             'idEncuestaPublicacionDestino' => $request['idEncuestaPublicacionDestino'][$i]);

             $data = array(
             'nombreEncuestaPublicacionDestino' => $request['nombreEncuestaPublicacionDestino'][$i],
             'correoEncuestaPublicacionDestino' => $request['correoEncuestaPublicacionDestino'][$i],
             'telefonoEncuestaPublicacionDestino' => $request['telefonoEncuestaPublicacionDestino'][$i],
             'EncuestaPublicacion_idEncuestaPublicacion' => $id);

            $destinos = \App\EncuestaPublicacionDestino::updateOrCreate($indice, $data);
            
            // totmaos el id del destinatario, si esta nulo o en cero, consultamos el ultimo insertado
            $idDestino = $request['idEncuestaPublicacionDestino'][$i];
            if($idDestino == null or $idDestino == 0)
            {
                $idDestino = \App\EncuestaPublicacionDestino::All()->last()->idEncuestaPublicacionDestino;
            }

            //********************************
            //
            // Envio de Correo con Encuesta
            //
            //********************************
            
            $datos['asunto'] = 'Encuesta: ';
            $datos['mensaje'] = 'Se ha enviado una encuesta
                <a href="http://'.$_SERVER["HTTP_HOST"].'/encuestapublicacion/2?accion=imprimir&P='.$id.'&D='.$idDestino.'">Ver encuesta</a>';
            $datos['correos'] = array($request['correoEncuestaPublicacionDestino'][$i]);

            Mail::send('emails.contact',$datos,function($msj) use ($datos)
            {
                $msj->to($datos['correos']);
                $msj->subject($datos['asunto']);
                // $msj->attach(public_path().'/plantrabajo.html');
            });
        }


        


    }


    protected function grabarRespuesta()
    {
        //antes de grabar las respuestas, eliminamos los datos de ella que puedieran existir
        \App\EncuestaPublicacionRespuesta::where('EncuestaPublicacionDestino_idEncuestaPublicacionDestino','=', Input::get('idEncuestaPublicacionDestino'))->delete();

        $dato = Input::get('respuesta');
        foreach ($dato as $reg => $valor) 
        {
            \App\EncuestaPublicacionRespuesta::create([
            'EncuestaPublicacionDestino_idEncuestaPublicacionDestino' => 
                    Input::get('idEncuestaPublicacionDestino'),
            'EncuestaPregunta_idEncuestaPregunta' => Input::get('idEncuestaPregunta')[$reg],
            'valorEncuestaPublicacionDestino' => $valor[0]
            ]);
        }
        
    }
}
