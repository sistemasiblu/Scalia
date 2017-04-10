<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\MensajeriaRequest;
use App\Http\Controllers\Controller;
use DB;
include public_path().'/ajax/consultarPermisos.php';
use Mail;

class MensajeriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vista = basename($_SERVER["PHP_SELF"].'?tipo='.$_GET['tipo']);
        $datos = consultarPermisos($vista);

        if($datos != null)
            return view('mensajeriagrid', compact('datos'));
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
        $radicado = \App\Radicado::All()->lists('codigoRadicado','idRadicado');
        return view('mensajeria', compact('radicado'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MensajeriaRequest $request)
    {
         \App\Mensajeria::create([
            'tipoCorrespondenciaMensajeria' => $request['tipoCorrespondenciaMensajeria'],
            'tipoEnvioMensajeria' => $request['tipoEnvioMensajeria'],
            'prioridadMensajeria' => $request['prioridadMensajeria'],
            'Radicado_idRadicado' => ($request['Radicado_idRadicado'] == '' || $request['Radicado_idRadicado'] == 0 ? NULL : $request['Radicado_idRadicado']),
            'fechaEnvioMensajeria' => $request['fechaEnvioMensajeria'],
            'descripcionMensajeria' => $request['descripcionMensajeria'],
            'transportadorMensajeria' => $request['transportadorMensajeria'],
            'Tercero_idTransportador' => $request['Tercero_idTransportador'],
            'estadoEntregaMensajeria' => $request['estadoEntregaMensajeria'],
            'destinatarioMensajeria' => $request['destinatarioMensajeria'],
            'Tercero_idDestinatario' => $request['Tercero_idDestinatario'],
            'fechaEntregaMensajeria' => $request['fechaEntregaMensajeria'],
            'direccionEntregaMensajeria' => $request['direccionEntregaMensajeria'],
            'seccionEntregaMensajeria' => $request['seccionEntregaMensajeria'],
            'fechaLimiteMensajeria' => $request['fechaLimiteMensajeria'],
            'numeroGuiaMensajeria' => $request['numeroGuiaMensajeria'],
            'Users_idCrea' => \Session::get('idUsuario'),
            'observacionMensajeria' => $request['observacionMensajeria']
            ]);

        $mensajeria = \App\Mensajeria::All()->last();

        $this->enviarEmail($mensajeria->idMensajeria, $request, 'creación');

        return redirect('/mensajeria?tipo='.$request['tipoEnvioMensajeria']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $mensajeria = \App\Mensajeria::find($id);

        $tipo = $_GET['tipo'];

        if ($tipo == 'datos') 
        {
            $datosMensajeria = DB::Select('
                SELECT 
                    tipoCorrespondenciaMensajeria,
                    codigoRadicado,
                    prioridadMensajeria,
                    fechaEnvioMensajeria,
                    descripcionMensajeria,
                    transportadorMensajeria,
                    destinatarioMensajeria,
                    direccionEntregaMensajeria,
                    estadoEntregaMensajeria,
                    seccionEntregaMensajeria,
                    fechaEntregaMensajeria,
                    fechaLimiteMensajeria,
                    numeroGuiaMensajeria,
                    observacionMensajeria,
                    nombreCentroTrabajo,
                    u.name as nombreUsuario
                FROM
                    mensajeria m
                        RIGHT JOIN
                    users u ON u.id = m.Users_idCrea
                        LEFT JOIN
                    Iblu.Tercero t ON u.Tercero_idasociado = t.idTercero
                        LEFT JOIN
                    Iblu.CentroTrabajo ct on t.CentroTrabajo_idCentroTrabajo = ct.idCentroTrabajo
                        LEFT JOIN
                    radicado r ON m.Radicado_idRadicado = r.idRadicado
                WHERE idMensajeria = '.$id.'
                GROUP BY idMensajeria');

            return view('formatos.impresionMensajeria',compact('datosMensajeria'));
        }
        else
        {

            $remitente = DB::Select('
                SELECT nombre1Tercero, direccionTercero, telefono1Tercero 
                FROM Iblu.Tercero
                WHERE idTercero = 512');

            $destinatario = DB::Select('
                SELECT 
                    destinatarioMensajeria as nombre1Tercero, 
                    direccionEntregaMensajeria as direccionTercero, 
                    seccionEntregaMensajeria
                FROM
                    mensajeria m
                WHERE
                    idMensajeria = '.$id);

            $codigoR = DB::Select('
                SELECT codigoRadicado
                FROM mensajeria m
                        LEFT JOIN
                    radicado r ON m.Radicado_idRadicado = m.idMensajeria
                WHERE idMensajeria = '.$id);

            return view('formatos.impresionStickerMensajeria',compact('remitente','destinatario','codigoR'));
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
        $mensajeria = \App\Mensajeria::find($id);
        $radicado = \App\Radicado::All()->lists('codigoRadicado','idRadicado');
        return view ('mensajeria',compact('radicado'),['mensajeria'=>$mensajeria]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MensajeriaRequest $request, $id)
    {
        $mensajeria = \App\Mensajeria::find($id);
        $mensajeria->fill($request->all());
        $mensajeria->Radicado_idRadicado = ($request['Radicado_idRadicado'] == '' || $request['Radicado_idRadicado'] == 0 ? NULL : $request['Radicado_idRadicado']);
        $mensajeria->Users_idModifica = \Session::get('idUsuario');
        $mensajeria->save();

        $this->enviarEmail($id, $request, 'modificación');
        
        return redirect('/mensajeria?tipo='.$request['tipoEnvioMensajeria']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MensajeriaRequest $request, $id)
    {
        \App\Mensajeria::destroy($id);
        $this->enviarEmail($id, $request, 'eliminación');
        return redirect('/mensajeria?tipo='.$request['tipoEnvioMensajeria']);
    }

    public function enviarEmail($id, $request, $accion)
    {
        $mail = array();

        if ($request['tipoEnvioMensajeria'] == 'Mensajero') 
        {
            $mail['destinatarioCorreo'] = 'yakeline.arango@ciiblu.com';
            $mail['asuntoCorreo'] = 'Mensajería';
            $mail['mensaje'] = 'Se ha hecho una <b>'.$accion.'</b> en el registro con id número <b>'.$id.'</b>.<br><br>
            <b>Tipo: </b>'.$request["tipoCorrespondenciaMensajeria"].'<br>
            <b>Prioridad: </b>'.$request["prioridadMensajeria"].'<br>
            <b>Estado: </b>'.$request["estadoEntregaMensajeria"].'<br>
            <b>Fecha de envío: </b>'.$request["fechaEnvioMensajeria"].'<br>
            <b>Descripcion: </b>'.$request["descripcionMensajeria"].'<br><br>

            <b>Destinatario: </b>'.$request["destinatarioMensajeria"].'<br>
            <b>Seccion: </b>'.$request["seccionEntregaMensajeria"].'<br>
            <b>Direccion: </b>'.$request["direccionEntregaMensajeria"].'<br>
            <b>Fecha limite de entrega: </b>'.$request["fechaLimiteMensajeria"].'<br><br>

            <b>Transportador: </b>'.$request["transportadorMensajeria"].'<br>
            <b>Guía: </b>'.$request["numeroGuiaMensajeria"].'<br>
            <b>Fecha real de entrega: </b>'.$request["fechaEntregaMensajeria"].'<br>
            <b>Realizado por: </b>'.\Session::get('nombreUsuario').'<br>
            <b>Observacion: </b>'.$request["observacionMensajeria"].'<br><br>

            O puede visualizar directamente la información del registro  <a href="'.$_SERVER["HTTP_HOST"].'/mensajeria/'.$id.'?tipo=datos">aquí</a>';

            Mail::send('emails.contact',$mail,function($msj) use ($mail)
            {
                $msj->to($mail['destinatarioCorreo']);
                $msj->subject($mail['asuntoCorreo']);
            });
        }
        else
        {
            $mail['destinatarioCorreo'] = 'auxiliarbodega@ciiblu.com';
            $mail['asuntoCorreo'] = 'Transporte';
            $mail['mensaje'] = 'Se ha hecho una <b>'.$accion.'</b> en el registro con id número <b>'.$id.'</b>.<br><br>
            <b>Tipo: </b>'.$request["tipoCorrespondenciaMensajeria"].'<br>
            <b>Prioridad: </b>'.$request["prioridadMensajeria"].'<br>
            <b>Estado: </b>'.$request["estadoEntregaMensajeria"].'<br>
            <b>Fecha de envío: </b>'.$request["fechaEnvioMensajeria"].'<br>
            <b>Descripcion: </b>'.$request["descripcionMensajeria"].'<br><br>

            <b>Destinatario: </b>'.$request["destinatarioMensajeria"].'<br>
            <b>Seccion: </b>'.$request["seccionEntregaMensajeria"].'<br>
            <b>Direccion: </b>'.$request["direccionEntregaMensajeria"].'<br>
            <b>Fecha limite de entrega: </b>'.$request["fechaLimiteMensajeria"].'<br><br>

            <b>Transportador: </b>'.$request["transportadorMensajeria"].'<br>
            <b>Guía: </b>'.$request["numeroGuiaMensajeria"].'<br>
            <b>Fecha real de entrega: </b>'.$request["fechaEntregaMensajeria"].'<br>
            <b>Realizado por: </b>'.\Session::get('nombreUsuario').'<br>
            <b>Observacion: </b>'.$request["observacionMensajeria"].'<br><br>

            O puede visualizar directamente la información del registro  <a href="'.$_SERVER["HTTP_HOST"].'/mensajeria/'.$id.'?tipo=datos">aquí</a>';

            Mail::send('emails.contact',$mail,function($msj) use ($mail)
            {
                $msj->to($mail['destinatarioCorreo']);
                $msj->subject($mail['asuntoCorreo']);
            });
        }
    }
}
