<?php 

$tipodocumento = $_POST['tipodocumento'];
$documento = $_POST['documento'];
$digitoVerificacion = $_POST['digitoVerificacion'];
$nombreA = $_POST['nombreA'];
$nombreB = $_POST['nombreB'];
$apellidoA = $_POST['apellidoA'];
$apellidoB = $_POST['apellidoB'];

/*Por post recibo los campos digitados en el modal de creación de terceros tipo PROVEEDOR
Ejecuto el insert into con todos los campos que no pueden ir nulos en la base de datos y estos 
son insertados con 0*/


DB::Select('INSERT INTO Iblu.Tercero 
	(idTercero, Compania_idCompania, TipoIdentificacion_idIdentificacion, Ciudad_idCiudad, Ciudad_idLugarExpedicion, fechaExpedicionDocumentoTercero, estaturaTercero, pesoTercero, direccionRutTercero, tipoVia1RutTercero, numeroVia1RutTercero, apendice1RutTercero, cardinalidad1RutTercero, tipoVia2RutTercero, numeroVia2RutTercero, apendice2RutTercero, cardinalidad2RutTercero, numeroPlacaRutTercero, complementoDireccionRutTercero, tipoVia1Tercero, numeroVia1Tercero, apendice1Tercero, cardinalidad1Tercero, tipoVia2Tercero, numeroVia2Tercero, apendice2Tercero, cardinalidad2Tercero, numeroPlacaTercero, complementoDireccionTercero, misionTercero, visionTercero, objetivoTercero, Tercero_idAsociado, FormaPago_idFormaPagoCompra, generaPuntosTercero, compraCITercero, codigoCIAsignadoTercero, fechaAsignacionCITercero, calcularRetencionFuenteProveedorSinBaseTercero, calcularRetencionIvaProveedorSinBaseTercero, calcularRetencionIcaProveedorSinBaseTercero, calcularRetencionFuenteClienteSinBaseTercero, calcularRetencionIvaClienteSinBaseTercero, calcularRetencionIcaClienteSinBaseTercero, calcularRetencionCreeProveedorSinBaseTercero, calcularRetencionCreeClienteSinBaseTercero, calcularIvaProveedorTercero, calcularIvaClienteTercero, calcularImpoconsumoProveedorTercero, calcularImpoconsumoClienteTercero, calcularRetencionFuenteClienteTercero, calcularRetencionFuenteProveedorTercero, calcularRetencionIvaClienteTercero, calcularRetencionIvaProveedorTercero, calcularRetencionIcaClienteTercero, calcularRetencionIcaProveedorTercero, calcularRetencionCreeClienteTercero, calcularRetencionCreeProveedorTercero, calculaCREETercero, ConceptoNomina_idSalud, ConceptoNomina_idSaludUPC, ConceptoNomina_idPension, ConceptoNomina_idFondoSolidaridad, ConceptoNomina_idAbonoVoluntario, diasPierdeEnfermedadGeneralTercero, diasPierdeAccidenteTrabajoTercero, porcentajeEnfermedadGeneralTercero, grupoSanguineoTercero, factorRHTercero, Ciudad_idLugarNacimiento, CentroCosto_idCentroCosto, Zona_idZona, Tercero_idSucursal, MacroCanal_idMacroCanal, CentroTrabajo_idCentroTrabajo, Tercero_idSalud, periodicidadSaludTercero, valorSaludUPCTercero, Tercero_idPension, periodicidadPensionTercero, valorAporteVoluntarioPensionTercero, Tercero_idCesantias, leyCesantiasTercero, fechaPagoUltimasCesantiasTercero, valorCesantiasAcumuladasTercero, valorSaldoCesantiasAcumuladasTercero, valorAnticipoCesantiasAcumuladasTercero, valorAnticipoCesantiasActualesTercero, carpetaArchivoExtractoBancarioTercero, jornadaLaboralDiaTercero, salarioBasicoTercero, registraTurnoTercero, tipoContratoLaboralTercero, GrupoNomina_idGrupoNomina, estadoCivilTercero, tipoSalarioTercero, ingresoFamiliarTercero, Turno_idTurno, longitudSSCCTercero, codigoEanPaisTercero, codigoEanEmpresaTercero, ultimoConsecutivoEanTercero, personasACargoTercero, habilitarConvenioTercero, NaturalezaJuridica_idNaturalezaJuridica, TipoAportanteNomina_idTipoAportanteNomina, extranjeroNoPensionTercero, colombianoResidenteExteriorTercero, TipoIdentificacion_idRepresentanteLegal, documentoRepresentanteLegalTercero, digitoVerificacionRepresentanteLegalTercero, nombreARepresentanteLegalTercero, nombreBRepresentanteLegalTercero, apellidoARepresentanteLegalTercero, apellidoBRepresentanteLegalTercero, Ciudad_idExpedicionRepresentanteLegalTercero, fechaRetiroPensionTercero, puntosTercero, esAutoretenedorCREETercero, noResponsableIVATercero, fechaFinBeneficio1429Tercero, ClasificacionRenta_idClasificacionRenta, resolucionDIANTercero, modalidadFacturacionTercero, tipoSolicitudFacturacionTercero, fechaResolucionTercero, prefijoResolucionTercero, sufijoResolucionTercero, numeroInicialResolucionTercero, numeroFinalResolucionTercero, advertenciaTercero, licenciaConduccionTercero, numeroLicenciaConduccionTercero, categoriaLicenciaConduccionTercero, numeroLibretaMilitarTercero, claseLibretaMilitarTercero, conductaLibretaMilitarTercero, tieneVehiculoTercero, tipoVehiculoTercero, disponibilidadViajeTercero, movilidadNacionalTercero, movilidadInternacionalTercero, tieneSeguridadSocialTercero, perfilLaboralTercero, tipoDescuentoFinancieroTercero, diasGraciaDescuentoFinancieroTercero, formatoChequeTercero, jerarquiaRolesTercero, tipoTercero, documentoTercero, digitoVerificacionTercero, nombreATercero, nombreBTercero, apellidoATercero, apellidoBTercero, nombre1Tercero) 
	VALUES(0, 512, '.$tipodocumento.', 0, 0, 0000-00-00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, "", "", "", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0000-00-00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, "", "", "", "", 0, 0000-00-00, 0, 0, 0, 0000-00-00, 0, 0, "", "", 0000-00-00, 0, 0, 0, 0, "", "No", "", "", 0, "", "", "No", "", "Si", 0, 0, 0, "", "Estricto", 0, 0, 0, "*02*",'.$documento.', '.$digitoVerificacion.', "'.$nombreA.'", "'.$nombreB.'", "'.$apellidoA.'", "'.$apellidoB.'", "'.$nombreA." ".$nombreB." ".$apellidoA." ".$apellidoB.'")');

/*Por ultimo envío un correo a la jefe de comercio exterior para que termine de completar el registro
en SAYA*/

$mail['para'] = 'comercioextiblu@ciiblu.com';
$mail['asunto'] = 'Notificación de Tercero en SAYA';
$mail['mensaje'] = 'Buen día <br><br>
					Se notifica la creación del tercero: <b>'.$nombreA." ".$nombreB." ".$apellidoA." ".$apellidoB.'</b> <br>
					con documento número: <b>'.$documento.'.</b> <br>
					Para realizar la respectiva verificación y completar los datos del Tercero.';


Mail::send('emails.contact',$mail,function($msj) use ($mail)
{
    $msj->to($mail['para']);
    $msj->subject($mail['asunto']);
}); 

echo json_encode("Guardado correctamente.");

?>