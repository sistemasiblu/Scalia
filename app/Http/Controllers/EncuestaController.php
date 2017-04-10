<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
//use App\Http\Requests\EncuestaRequest;

use App\Http\Controllers\Controller;

use DB;
use Carbon;
include public_path().'/ajax/consultarPermisos.php';


class EncuestaController extends Controller
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

        if($datos != null)
            return view('encuestagrid', compact('datos'));
        else
            return view('accesodenegado');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('encuesta');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request['respuesta'] != 'falso')
        { 
            $fechahora = Carbon\Carbon::now();

            // Insertamos el encabezado
            \App\Encuesta::create([
                'tituloEncuesta' => $request['tituloEncuesta'],
                'descripcionEncuesta' => $request['descripcionEncuesta'],
                'Users_idCrea' => \Session::get('idUsuario'),
                'created_at' => $fechahora,
                'Compania_idCompania' => \Session::get('idCompania')
                ]);

            // Consultamos el ultimo id insertado
            $encuesta = \App\Encuesta::All()->last();
            
            // ejecutamos la funcion para grabar las preguntas y sus opciones
            $this->grabarDetalle($encuesta->idEncuesta, $request);


            return redirect('/encuesta');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $encuesta = \App\Encuesta::find($id);
        $encuestaDetalle = DB::table('encuesta as E')
        ->leftjoin('encuestapregunta as EP', 'E.idEncuesta', '=', 'EP.Encuesta_idEncuesta')
        ->leftjoin('encuestaopcion as EO', 'EP.idEncuestaPregunta', '=', 'EO.EncuestaPregunta_idEncuestaPregunta')
        ->select(DB::raw('idEncuestaPregunta, preguntaEncuestaPregunta, detalleEncuestaPregunta, tipoRespuestaEncuestaPregunta, Encuesta_idEncuesta, idEncuestaOpcion, valorEncuestaOpcion, nombreEncuestaOpcion, EncuestaPregunta_idEncuestaPregunta'))
        ->where('idEncuesta','=',$id)
        ->get();

        $encuestaRol = DB::table('encuesta as E')
        ->leftjoin('encuestarol as ER', 'E.idEncuesta', '=', 'ER.Encuesta_idEncuesta')
        ->leftjoin('rol as R', 'ER.Rol_idRol', '=', 'R.idRol')
        ->select(DB::raw('idEncuestaRol, Rol_idRol, nombreRol, adicionarEncuestaRol, modificarEncuestaRol, eliminarEncuestaRol, consultarEncuestaRol, publicarEncuestaRol'))
        ->where('idEncuesta','=',$id)
        ->get();


        return view('encuesta',['encuesta'=>$encuesta], compact('encuestaDetalle','encuestaRol'));
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
        if($request['respuesta'] != 'falso')
        {
            $fechahora = Carbon\Carbon::now();

            $encuesta = \App\Encuesta::find($id);
            $encuesta->fill($request->all());
            $encuesta->updated_at = $fechahora;
            $encuesta->Users_idModifica = \Session::get('idUsuario');
            $encuesta->save();

            // ejecutamos la funcion para grabar las preguntas y sus opciones
            $this->grabarDetalle($id, $request);


            return redirect('/encuesta');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Encuesta::destroy($id);
        return redirect('/encuesta');
    }

    protected function grabarDetalle($id, $request)
    {

        // en el formulario hay un campo oculto en el que almacenamos los 
        // id que se eliminan separados por coma, en este proceso lo convertimos 
        // en array y eliminamos dichos id de la tabla de detalle preguntas
        $idsEliminar = explode(',', $request['eliminarPregunta']);
        \App\EncuestaPregunta::whereIn('idEncuestaPregunta',$idsEliminar)->delete();


        for($i = 0; $i < count($request['idEncuestaPregunta']); $i++)
        {
           
            $indice = array(
             'idEncuestaPregunta' => $request['idEncuestaPregunta'][$i]);

            $data = array(
             'preguntaEncuestaPregunta' => $request['preguntaEncuestaPregunta'][$i],
             'detalleEncuestaPregunta' => $request['detalleEncuestaPregunta'][$i],
             'tipoRespuestaEncuestaPregunta' => $request['tipoRespuestaEncuestaPregunta'][$i],
             'Encuesta_idEncuesta' => $id);

            $preguntas = \App\EncuestaPregunta::updateOrCreate($indice, $data);

            // Consultamos el ultimo id insertado
            $idPregunta = $request['idEncuestaPregunta'][$i];
            if($idPregunta == 0)
            {
                $encuesta = \App\EncuestaPregunta::All()->last();
                $idPregunta = $encuesta->idEncuestaPregunta;
            }
            
            // por cada pregunta, gurdamos el subdetalle (Opciones de la pregunta)
            $this->grabarSubDetalle($idPregunta, $request, $i);

            // también verificamos si el usuario cambió el tipo de respuesta por una que no sea multiple
            // en este caso eliminamos las posibles opciones que pudiera tener ya esa pregunta
            $multiples = ['Selección Múltiple','Casillas de Verificación','Lista de Opciones'];
            if(in_array($request['tipoRespuestaEncuestaPregunta'][$i], $multiples)  === false)
            {
                echo 'EncuestaPregunta_idEncuestaPregunta'.'='.$request['idEncuestaPregunta'][$i];
                \App\EncuestaOpcion::where('EncuestaPregunta_idEncuestaPregunta','=',$request['idEncuestaPregunta'][$i])->delete();

            }

        }


        // en el formulario hay un campo oculto en el que almacenamos los id que se eliminan separados por coma
        // en este proceso lo convertimos en array y eliminamos dichos id de la tabla de detalle
        $idsEliminar = explode(',', $request['eliminarRol']);
        \App\EncuestaRol::whereIn('idEncuestaRol',$idsEliminar)->delete();

        $contador = count($request['idEncuestaRol']);

        for($i = 0; $i < $contador; $i++)
        {

            $indice = array(
             'idEncuestaRol' => $request['idEncuestaRol'][$i]);

            $data = array(
            'Encuesta_idEncuesta' => $id,
            'Rol_idRol' => $request['Rol_idRol'][$i],
            'adicionarEncuestaRol' => $request['adicionarEncuestaRol'][$i],
            'modificarEncuestaRol' => $request['modificarEncuestaRol'][$i],
            'consultarEncuestaRol' => $request['consultarEncuestaRol'][$i],
            'eliminarEncuestaRol' => $request['eliminarEncuestaRol'][$i],
            'publicarEncuestaRol' => $request['publicarEncuestaRol'][$i]);
            $permisos = \App\EncuestaRol::updateOrCreate($indice, $data);

        }
        
    }


    protected function grabarSubDetalle($id, $request, $i)
    {

        // en el formulario hay un campo oculto en el que almacenamos los 
        // id que se eliminan separados por coma, en este proceso lo convertimos 
        // en array y eliminamos dichos id de la tabla de detalle preguntas
        $idsEliminar = explode(',', $request['eliminarOpcion']);
        \App\EncuestaOpcion::whereIn('idEncuestaOpcion',$idsEliminar)->delete();

        if(isset($request['idEncuestaOpcion'][$i]))
        {
            for($j = 0; $j < count($request['idEncuestaOpcion'][$i]); $j++)
            {
                $indice = array(
                 'idEncuestaOpcion' => $request['idEncuestaOpcion'][$i][$j]);

                $data = array(
                 'valorEncuestaOpcion' => $request['valorEncuestaOpcion'][$i][$j],
                 'nombreEncuestaOpcion' => $request['nombreEncuestaOpcion'][$i][$j],
                 'EncuestaPregunta_idEncuestaPregunta' => $id);

                $preguntas = \App\EncuestaOpcion::updateOrCreate($indice, $data);
            }
        }
    }

}
