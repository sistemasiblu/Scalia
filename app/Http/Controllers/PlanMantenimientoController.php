<?php

namespace App\Http\Controllers;
use Illuminate\Support\CollectionStdClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Mail;

class PlanMantenimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('planmantenimientogrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $planmantenimiento=\App\PlanMantenimiento::All();
        $activo=\App\Activo::where('clasificacionActivo','=','Activo')->lists('nombreActivo','idActivo');
        $parte=\App\Activo::where('clasificacionActivo','=','Parte')->lists('nombreActivo','idActivo');
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $tiposervicio=\App\Tiposervicio::lists('nombreTipoServicio','idTipoServicio');

        return view('planmantenimiento1', compact('activo','parte','tipoaccion','tiposervicio','plmantenimiento'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        \App\PlanMantenimientoAlerta::create(
        [
        'nombrePlanMantenimientoAlerta'=>$request['correoParaPlanMantenimientoAlerta'], 
        'correoParaPlanMantenimientoAlerta'=>$request['correoParaPlanMantenimientoAlerta'],
        'correoCopiaPlanMantenimientoAlerta'=>$request['correoCopiaPlanMantenimientoAlerta'],
        'correoCopiaOcultaPlanMantenimientoAlerta'=>$request['correoCopiaOcultaPlanMantenimientoAlerta'],
        'correoAsuntoPlanMantenimientoAlerta'=>$request['correoAsuntoPlanMantenimientoAlerta'],
        'correoMensajePlanMantenimientoAlerta'=>$request['correoMensajePlanMantenimientoAlerta'],
        'tareaFechaInicioPlanMantenimientoAlerta'=>$request['tareaFechaInicioPlanMantenimientoAlerta'],
        'tareaHoraPlanMantenimientoAlerta'=>$request['tareaHoraPlanMantenimientoAlerta'], 
        'tareaDiaLaboralPlanMantenimientoAlerta'=>$request['tareaDiaLaboralPlanMantenimientoAlerta'],
        'tareaIntervaloPlanMantenimientoAlerta'=>$request['tareaIntervaloPlanMantenimientoAlerta'],
        'tareaDiasPlanMantenimientoAlerta'=>$request['numeroDias'],
        'tareaMesesPlanMantenimientoAlerta'=>$request['numeroMeses']
        ]);
        $ultimoPlanMantAlerta=\App\PlanMantenimientoAlerta::All()->last();

       \App\PlanMantenimiento::create(
        [
       'Activo_idActivo'=>$request['Activo_idActivo'],
       'actividadPlanMantenimiento'=>$request['actividadPlanMantenimiento'],
       'PlanMantenimientoAlerta_idPlanMantenimientoAlerta'=>$ultimoPlanMantAlerta->idPlanMantenimientoAlerta, 
       'TipoServicio_idTipoServicio'=>$request['TipoServicio_idTipoServicio'], 
       'TipoAccion_idTipoAccion'=>$request['TipoAccion_idTipoAccion'], 
       'prioridadPlanMantenimiento'=>$request['prioridadPlanMantenimiento'], 
       'tiempotareaPlanMantenimiento'=>$request['tiempotareaPlanMantenimiento'],
       'diasparoPlanMantenimiento'=>$request['diasparoPlanMantenimiento'], 
       'procedimientoPlanMantenimiento'=>$request['procedimientoPlanMantenimiento'],
        ]);

       //guarda el valor de los campos enviados desde el form en un array
       $data = $request->all();
 
       //se envia el array y la vista lo recibe en llaves individuales {{ $email }} , {{ $subject }}...
         //remitente
           Mail::send("emails.template", [], function($message) {
        $message->to("jorge@aprendible.com", "Luis Garcia")
        ->subject("Bienvenido a Aprendible!");
    });
       

        $plMantenimientoultimo = \App\PlanMantenimiento::All()->last();

        echo count($request['idActivoParte']);
        for ($i=0 ; $i < count($request['idActivoParte']); $i++)
        {
            \App\PlanMantenimientoParte::create(
            [
             'Activo_idActivo'=>$request['Activo_idActivo'],
             'Activo_idParte'=>$request['Activo_idParte'][$i],
             'PlanMantenimiento_idPlanMantenimiento'=>$plMantenimientoultimo->idPlanMantenimiento,             
            ]); 

        }

       

       return Redirect('/planmantenimiento');
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
         $plmantenimientoalerta=DB::Select(
        "select 
       idPlanMantenimientoAlerta, nombrePlanMantenimientoAlerta, correoParaPlanMantenimientoAlerta, correoCopiaPlanMantenimientoAlerta, correoCopiaOcultaPlanMantenimientoAlerta, correoAsuntoPlanMantenimientoAlerta, correoMensajePlanMantenimientoAlerta, tareaFechaInicioPlanMantenimientoAlerta, tareaHoraPlanMantenimientoAlerta, tareaDiaLaboralPlanMantenimientoAlerta, tareaIntervaloPlanMantenimientoAlerta, tareaDiasPlanMantenimientoAlerta, tareaMesesPlanMantenimientoAlerta
        from planmantenimiento 
        inner join planmantenimientoalerta
        on planmantenimiento.PlanMantenimientoAlerta_idPlanMantenimientoAlerta=planmantenimientoalerta.idPlanMantenimientoAlerta
        where idPlanMantenimiento=".$id);
         for ($i=0 ; $i < count( $plmantenimientoalerta); $i++) 
    {  
        $planmantenimientoAlerta[] = get_object_vars($plmantenimientoalerta[$i]);
    }

    $plmantenimientoParte=DB::Select(
        "select 
        planmantenimientoparte.idPlanMantenimientoParte, planmantenimientoparte.Activo_idParte,  planmantenimientoparte.PlanMantenimiento_idPlanMantenimiento, activo.nombreActivo as nombreActivoParte
        from activo
       inner join planmantenimientoparte
        on  planmantenimientoparte.Activo_idParte=activo.idActivo
        inner join planmantenimiento
        on planmantenimientoparte.PlanMantenimiento_idPlanMantenimiento=PlanMantenimiento.idPlanMantenimiento
        where planmantenimientoparte.Activo_idActivo=".$id);

   

    for ($i=0 ; $i < count( $plmantenimientoParte); $i++) 
    {  
        $planmantenimientoParte[] = get_object_vars($plmantenimientoParte[$i]);
    }

        $plmantenimiento=\App\PlanMantenimiento::find($id);
        $activo=\App\Activo::where('clasificacionActivo','=','Activo')->lists('nombreActivo','idActivo');
        $parte=\App\Activo::where('clasificacionActivo','=','Parte')->lists('nombreActivo','idActivo');
        $tipoaccion=\App\TipoAccion::lists('nombreTipoAccion','idTipoAccion');
        $tiposervicio=\App\Tiposervicio::lists('nombreTipoServicio','idTipoServicio');

        return view('planmantenimiento1', compact('activo','parte','tipoaccion','tiposervicio','plmantenimiento','planmantenimientoAlerta','planmantenimientoParte'));

        return Redirect('/planmantenimiento');

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
        $activo=\App\PlanMantenimiento::find($id);
        $activo->fill($request->all());
        $activo->save();

         return redirect('/planmantenimiento');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\PlanMantenimiento::destroy($id);
        return redirect('/planmantenimiento');
    }
}
