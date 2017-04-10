<?php
    $consultaFormulario = DB::table('documentopropiedad')
    ->select(DB::raw('tituloMetadato, Documento_idDocumento, tipoMetadato'))
    ->leftjoin ('documento', 'documentopropiedad.Documento_idDocumento', "=", 'documento.idDocumento')
    ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
    ->where ('idDocumento', "=", $_GET['idDoc'])
    ->where('gridDocumentoPropiedad', "=", 1)
    ->where('tipoDocumento', "=", 1)
    ->get();


    $idDoc = 0;
    $campos = 'Radicado_idRadicado, ';
    $lista = '';
    foreach ($consultaFormulario as $key => $value) 
    { 
        $formulario = get_object_vars($value);

        $idDoc =$formulario['Documento_idDocumento'];
        
        if ($formulario["tipoMetadato"] == 'Lista') 
        {
            $campos .="MAX(".$formulario["tituloMetadato"].".nombreSubLista) as `".$formulario["tituloMetadato"]."`,";
            $lista .= ' left join sublista as '.$formulario["tituloMetadato"].'
                    on radicadodocumentopropiedad.valorRadicadoDocumentoPropiedad = '.$formulario["tituloMetadato"].'.idSubLista';
        }
        else
        {
            $campos .= "MAX(IF(tituloMetadato = '".$formulario["tituloMetadato"]."', 
                    IF(tipoMetadato = \"Editor\", editorRadicadoDocumentoPropiedad, valorRadicadoDocumentoPropiedad) ,
                    '')) as `".$formulario["tituloMetadato"]."`,";

        }
    }
    // $campos = substr($campos, 0, strlen($campos)-1);

    $idDoc = $formulario['Documento_idDocumento'];
    $sql = 'Select radicadodocumentopropiedad.'.$campos.' idRadicadoDocumentoPropiedad
         from(
                Select  
                    Radicado_idRadicado, 
                    numeroRadicadoVersion, 
                    idRadicadoVersion  
                from (
                        Select 
                        Radicado_idRadicado, 
                        numeroRadicadoVersion, 
                        idRadicadoVersion  
                        from radicadoversion
                        group by Radicado_idRadicado, numeroRadicadoVersion desc
                    ) as ver
                    
                    group by Radicado_idRadicado
                ) as datos
                    
                left join radicadodocumentopropiedad
                on datos.Radicado_idRadicado = radicadodocumentopropiedad.Radicado_idRadicado 
                and radicadodocumentopropiedad.RadicadoVersion_idRadicadoVersion = datos.idRadicadoVersion

                left join documentopropiedad 
                on radicadodocumentopropiedad.DocumentoPropiedad_idDocumentoPropiedad = documentopropiedad.idDocumentoPropiedad

                left join metadato 
                on documentopropiedad.Metadato_idMetadato = metadato.idMetadato

                left join documento
                on documentopropiedad.Documento_idDocumento = documento.idDocumento
                '.$lista.'
                where Documento_idDocumento = '. $_GET['idDoc'].'
                group by Radicado_idRadicado';


    $valoresformulario = DB::select($sql);

    $row = array();

    foreach ($valoresformulario as $key => $value) 
    {  
        $valorformulario = get_object_vars($value);
        $row[$key][] = 
                        '<a style="cursor:pointer" onclick="divVersionFormulario();llamarMetadatosFormulario('.$value->Radicado_idRadicado.',\'\'); listarVersiones('.$value->Radicado_idRadicado.');">'.
                            '<span<i class="fa fa-pencil"></i></span>'.
                        '</a>&nbsp;'.
                        '<a style="cursor:pointer" onclick="eliminarFormulario('.$value->Radicado_idRadicado.')">'.
                            '<span<i class="fa fa-trash"></i></span>'.
                        '</a>&nbsp;';
                        
        foreach ($value as $pos => $campo) 
        {
            $row[$key][] = $campo;
        }
        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>
