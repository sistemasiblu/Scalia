<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AgendaPermisoRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';

class AgendaPermisoController extends Controller
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
            return view('agendapermisogrid', compact('datos'));
        else
            return view('accesodenegado');
        // return view('agendapermisogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usuario = \App\User::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('name', 'id');

        $idUsuario = \App\User::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('id');
        $nombreUsuario = \App\User::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('name');

        $idCategoriaAgenda = \App\CategoriaAgenda::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('idCategoriaAgenda');

        $nombreCategoriaAgenda = \App\CategoriaAgenda::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreCategoriaAgenda');

        return view('agendapermiso',compact('usuario','idUsuario','nombreUsuario','idCategoriaAgenda','nombreCategoriaAgenda'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgendaPermisoRequest $request)
    {
        if($request['respuesta'] != 'falso')
        {    
            \App\AgendaPermiso::create([
                'Users_idAutorizado' => $request['Users_idAutorizado'],
            ]);

            $agendapermiso = \App\AgendaPermiso::All()->last();

            $this->grabarDetalle($agendapermiso->idAgendaPermiso, $request);

            return redirect('agendapermiso');
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
        $usuario = \App\User::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('name', 'id');

        $idUsuario = \App\User::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('id');
        $nombreUsuario = \App\User::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('name');

        $idCategoriaAgenda = \App\CategoriaAgenda::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('idCategoriaAgenda');

        $nombreCategoriaAgenda = \App\CategoriaAgenda::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('nombreCategoriaAgenda');

        $agendapermiso = \App\AgendaPermiso::find($id);

        $agendapermisodetalle = DB::Select('
            SELECT 
                Users_idPropietario,
                CategoriaAgenda_idCategoriaAgenda,
                adicionarAgendaPermisoDetalle,
                modificarAgendaPermisoDetalle,
                eliminarAgendaPermisoDetalle,
                consultarAgendaPermisoDetalle,
                idAgendaPermisoDetalle,
                AgendaPermiso_idAgendaPermiso
            FROM agendapermisodetalle
            WHERE AgendaPermiso_idAgendaPermiso ='.$id);

        return view('agendapermiso',compact('usuario','idUsuario','nombreUsuario','idCategoriaAgenda','nombreCategoriaAgenda','agendapermisodetalle'),['agendapermiso' => $agendapermiso]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgendaPermisoRequest $request, $id)
    {
        if($request['respuesta'] != 'falso')
        {    
            $agendapermiso = \App\AgendaPermiso::find($id);
            $agendapermiso->fill($request->all());
            $agendapermiso->save();

            $this->grabarDetalle($id, $request);

            return redirect('/agendapermiso');
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
        \App\AgendaPermiso::destroy($id);
        return redirect('/agendapermiso');
    }

    protected function grabarDetalle($id, $request)
    {
        $idsEliminar = explode(',', $request['eliminarAgendaPermiso']);
        \App\AgendaPermisoDetalle::whereIn('idAgendaPermisoDetalle',$idsEliminar)->delete();

        $contadorAgendaPermiso = count($request['adicionarAgendaPermisoDetalle']);
        for($i = 0; $i < $contadorAgendaPermiso; $i++)
        {

            $indice = array(
             'idAgendaPermisoDetalle' => $request['idAgendaPermisoDetalle'][$i]);

            $data = array(
             'AgendaPermiso_idAgendaPermiso' => $id,
             'Users_idPropietario' => $request['Users_idPropietario'][$i],
             'CategoriaAgenda_idCategoriaAgenda' => $request['CategoriaAgenda_idCategoriaAgenda'][$i],
             'adicionarAgendaPermisoDetalle' => $request['adicionarAgendaPermisoDetalle'][$i],
             'modificarAgendaPermisoDetalle' => $request['modificarAgendaPermisoDetalle'][$i],
             'eliminarAgendaPermisoDetalle' => $request['eliminarAgendaPermisoDetalle'][$i],
             'consultarAgendaPermisoDetalle' => $request['consultarAgendaPermisoDetalle'][$i]);

            $preguntas = \App\AgendaPermisoDetalle::updateOrCreate($indice, $data);

        }

    }
}
