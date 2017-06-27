<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AgendaRequest;
use App\Http\Controllers\Controller;
use DB;
use Mail;
use Ical\Ical;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('agenda');
    }

    public function indexGrid()
    {
        return view('agendagrid');
    }

    public function getAll()
    {
        $events = $this->obtenerDatosEvento();
        echo json_encode(
            array(
                "success" => 1,
                "result" => $events
            )
        );
    }

    public function obtenerDatosEvento()
    {
        $query = DB::Select('
                SELECT 
                    idAgenda as id, asuntoAgenda as title, urlAgenda as url, codigoCategoriaAgenda as class, fechaHoraInicioAgenda as start, fechaHoraFinAgenda as end, detallesAgenda as body, "si" as event
                FROM agenda a
                    LEFT JOIN 
                categoriaagenda ca ON a.CategoriaAgenda_idCategoriaAgenda = ca.idCategoriaAgenda
                WHERE a.Compania_idCompania = '.\Session::get('idCompania'));        
        
        if(count($query) > 0)
        {
            return $query;
        }
    }

    public function indexAgendaEvento()
    {
        $agendaSeguimiento = '';
        $agendaAsistente = '';
        if(isset($_GET['id']))
        {
            $agendaSeguimiento = DB::Select('
                SELECT 
                    idAgendaSeguimiento, 
                    Agenda_idAgenda, 
                    fechaHoraAgendaSeguimiento, 
                    Users_idCrea, 
                    detallesAgendaSeguimiento
                FROM
                    agendaseguimiento
                WHERE Agenda_idAgenda = '.$_GET['id']);

            $agendaAsistente = DB::Select('
                SELECT 
                    idAgendaAsistente,
                    Agenda_idAgenda, 
                    Tercero_idAsistente, 
                    IFNULL(
                        nombreCompletoTercero, nombreAgendaAsistente
                    ) as nombreAgendaAsistente, 
                    IFNULL(
                        correoElectronicoTercero, correoElectronicoAgendaAsistente
                    ) as correoElectronicoAgendaAsistente 
                FROM 
                    agendaasistente ags 
                    LEFT JOIN tercero T ON ags.Tercero_idAsistente = T.idTercero 
                WHERE 
                    Agenda_idAgenda = '.$_GET['id']);
        }

        $categoriaagenda = \App\CategoriaAgenda::where('Compania_idCompania','=',\Session::get('idCompania'))->lists('nombreCategoriaAgenda','idCategoriaAgenda');
        $casocrm = \App\MovimientoCRM::where('Compania_idCompania','=', \Session::get('idCompania'))->lists('asuntoMovimientoCRM','idMovimientoCRM');
        $supervisor = \App\Tercero::where('Compania_idCompania','=',\Session::get('idCompania'))->lists('nombreCompletoTercero','idTercero');
        $responsable = \App\Tercero::where('Compania_idCompania','=',\Session::get('idCompania'))->lists('nombreCompletoTercero','idTercero');
        return view('agregareventocalendario',compact('categoriaagenda','supervisor','casocrm','responsable','agendaSeguimiento', 'agendaAsistente'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgendaRequest $request)
    {
        // require_once 'vendor/autoload.php';

        $fechaInicio =  strtotime(substr($request['fechaHoraInicioAgenda'], 6, 4)."-".substr($request['fechaHoraInicioAgenda'], 3, 2)."-".substr($request['fechaHoraInicioAgenda'], 0, 2)." " .substr($request['fechaHoraInicioAgenda'], 10, 6)) * 1000;

        $fechaFin =  strtotime(substr($request['fechaHoraFinAgenda'], 6, 4)."-".substr($request['fechaHoraFinAgenda'], 3, 2)."-".substr($request['fechaHoraFinAgenda'], 0, 2)." " .substr($request['fechaHoraFinAgenda'], 10, 6)) * 1000;

        $indice = array(
            'idAgenda' => $request['idAgenda']);

        $data = array(
            'CategoriaAgenda_idCategoriaAgenda' => $request['CategoriaAgenda_idCategoriaAgenda'],
            'asuntoAgenda' => ($request['asuntoAgenda'] == ''  ? NULL : $request['asuntoAgenda']),
            'fechaHoraInicioAgenda' => $fechaInicio,
            'fechaHoraFinAgenda' => $fechaFin,
            'Tercero_idSupervisor' => $request['Tercero_idSupervisor'],
            'Tercero_idResponsable' => ($request['Tercero_idResponsable'] == '' ? NULL : $request['Tercero_idResponsable']),
            'MovimientoCRM_idMovimientoCRM' => ($request['MovimientoCRM_idMovimientoCRM'] == '' ? NULL : $request['MovimientoCRM_idMovimientoCRM']),
            'ubicacionAgenda' => ($request['ubicacionAgenda'] == '' ? NULL : $request['ubicacionAgenda']),
            'porcentajeEjecucionAgenda' => ($request['porcentajeEjecucionAgenda'] == '' ? NULL : $request['porcentajeEjecucionAgenda']),
            'detallesAgenda' => ($request['detallesAgenda'] == '' ? NULL : $request['detallesAgenda']),
            'estadoAgenda' => ($request['estadoAgenda'] == '' ? NULL : $request['estadoAgenda']),
            'Compania_idCompania' => \Session::get('idCompania'));

        $preguntas = \App\Agenda::updateOrCreate($indice, $data);

        if ($request['idAgenda'] != '') 
        {
            $this->grabarDetalle($request['idAgenda'],$request);
        }
        else
        {
            $agenda = \App\Agenda::All()->last();
            DB::update('UPDATE agenda SET urlAgenda = "http://'.$_SERVER["HTTP_HOST"].'/eventoagenda?id='.$agenda->idAgenda.'" WHERE idAgenda = '.$agenda->idAgenda);
            $this->grabarDetalle($agenda->idAgenda,$request);

            // try 
            // {
            //     $ical = (new Ical())->setAddress('Colombia')
            //             ->setDateStart("'".$request["fechaHoraInicioAgenda"]."'")
            //             ->setDateEnd("'".$request["fechaHoraFinAgenda"]."'")
            //             ->setDescription("'".$request["asuntoAgenda"]."'")
            //             ->setSummary('Running')
            //             ->setFilename(uniqid());
            //     $ical->addHeader();
                   
            //     echo $ical->getICAL();          
              
            // } 
            // catch (\Exception $exc) 
            // {
            //     echo $exc->getMessage();
            // }
        }
    }

    private function _formatDate($date)
    {
        return strtotime(substr($date, 6, 4)."-".substr($date, 3, 2)."-".substr($date, 0, 2)." " .substr($date, 10, 6)) * 1000;
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
        //
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
        $agenda = \App\Agenda::find($id);
        $agenda->fill($request->all());
        $agenda->save();

        if($request->ajax()) 
        {
            return response()->json(['Evento creado correctamente']);
        }
        return redirect('/agenda');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\Agenda::destroy($id);
        return response()->json(['Cancelado correctamente.']);
    }

    public function grabarDetalle($id, $request)
    {
        $idsEliminar = explode(',', $request['eliminarAgendaSeguimiento']);
        \App\AgendaSeguimiento::whereIn('idAgendaSeguimiento',$idsEliminar)->delete();

        $contador = count($request['idAgendaSeguimiento']);

        for($i = 0; $i < $contador; $i++)
        {

            $indice = array(
             'idAgendaSeguimiento' => $request['idAgendaSeguimiento'][$i]);

            $data = array(
            'Agenda_idAgenda' => $id,
            'fechaHoraAgendaSeguimiento' => $request['fechaHoraAgendaSeguimiento'][$i],
            'Users_idCrea' => \Session::get('idUsuario'),
            'detallesAgendaSeguimiento' => $request['detallesAgendaSeguimiento'][$i]);

             $preguntas = \App\AgendaSeguimiento::updateOrCreate($indice, $data);

        }

        $idsEliminar = explode(',', $request['eliminarAgendaAsistente']);
        \App\AgendaAsistente::whereIn('idAgendaAsistente',$idsEliminar)->delete();

        $contador = count($request['idAgendaAsistente']);

        $destinatario = '';
        for($i = 0; $i < $contador; $i++)
        {

            $indice = array(
             'idAgendaAsistente' => $request['idAgendaAsistente'][$i]);

            $data = array(
            'Agenda_idAgenda' => $id,
            'Tercero_idAsistente' => ($request['Tercero_idAsistente'][$i] == 0 ? NULL : $request['Tercero_idAsistente'][$i]),
            'nombreAgendaAsistente' => ($request['nombreAgendaAsistente'][$i] == '' ? NULL : $request['nombreAgendaAsistente'][$i]),
            'correoElectronicoAgendaAsistente' => ($request['correoElectronicoAgendaAsistente'][$i] == '' ? NULL : $request['correoElectronicoAgendaAsistente'][$i]));

            $preguntas = \App\AgendaAsistente::updateOrCreate($indice, $data);

            $destinatario = $request['correoElectronicoAgendaAsistente'][$i].',';
        }

        if ($destinatario != '') 
        {
            $destinatario = substr($destinatario, 0, -1);
            $mail = array();
            $mail['asuntoCorreoAgenda'] = 'Agenda';
            $mail['mensaje'] = "Se han realizado movimientos en su agenda.<br><br>
            Para visualizarlo mejor <a href='http://".$_SERVER['HTTP_HOST']."/agenda'>ve directamente</a> a la agenda.";
            $mail['destinatarioCorreoAgenda'] = explode(',', $destinatario);
            Mail::send('emails.contact',$mail,function($msj) use ($mail)
            {
                $msj->to($mail['destinatarioCorreoAgenda']);
                $msj->subject($mail['asuntoCorreoAgenda']);
            });             
        }


        if($request->ajax()) 
        {
            return response()->json(['Evento creado correctamente']);
        }
        // return redirect('/agenda');
        // header("Refresh:0");
        echo "<script type='text/javascript'>window.parent.location.reload()</script>";
    }
}
