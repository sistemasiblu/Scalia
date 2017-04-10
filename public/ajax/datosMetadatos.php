<?php
    $consultaMetadatos = DB::table('documentopropiedad')
    ->leftjoin('metadato','documentopropiedad.Metadato_idMetadato', "=", 'metadato.idMetadato')
    ->select(DB::raw('metadato.*, Documento_idDocumento'))
    ->where ('Documento_idDocumento', "=", $_GET['idDoc'])
    ->where('gridDocumentoPropiedad', "=", 1)
    ->get();

    $idDoc = 0;
    $campos = 'Radicado_idRadicado, ';
    $lista = '';

    $consulta = $_GET["consulta"] != null ? ' and '.$_GET["consulta"] : '';
    $consulta = str_replace("*", "'%", $consulta);
    $consulta = str_replace("-", "%'", $consulta);
    $consulta = str_replace(".", "'", $consulta);
    $consulta = str_replace(",", "'", $consulta);

    foreach ($consultaMetadatos as $key => $value) 
    { 
        $metadatos = get_object_vars($value);

        $idDoc =$metadatos['Documento_idDocumento'];
        
        if ($metadatos["tipoMetadato"] == 'Lista') 
        {
            $campos .="MAX(nombreSubLista) as `".$metadatos["tituloMetadato"]."`,";
            $lista .= 'left join sublista
                    on radicadodocumentopropiedad.valorRadicadoDocumentoPropiedad = sublista.idSubLista';
        }
        else
        {
            $campos .= "MAX(IF(tituloMetadato = '".$metadatos["tituloMetadato"]."', 
                    IF(tipoMetadato = \"Editor\", editorRadicadoDocumentoPropiedad, valorRadicadoDocumentoPropiedad) ,
                    '')) as ".str_replace(" ","_",$metadatos["tituloMetadato"]).",";

        }
    }
    // $campos = substr($campos, 0, strlen($campos)-1);

    $idDoc = $metadatos['Documento_idDocumento'];
    $sql = 'Select '.$campos.' idRadicadoDocumentoPropiedad
         From radicadodocumentopropiedad
         left join documentopropiedad 
         on radicadodocumentopropiedad.DocumentoPropiedad_idDocumentoPropiedad = documentopropiedad.idDocumentoPropiedad
         left join metadato 
         on documentopropiedad.Metadato_idMetadato = metadato.idMetadato
         '.$lista.'
         where Documento_idDocumento = '. $_GET['idDoc'].''.$consulta.'
         group By Radicado_idRadicado';

    $valoresmetadatos = DB::select($sql);

    $row = array();

    foreach ($valoresmetadatos as $key => $value) 
    {  
        $valormetadatos = get_object_vars($value);
        $row[$key][] = 
                        '<a style="cursor:pointer" onclick="activarDiv(); llamarPreview('.$value->Radicado_idRadicado.','.$idDoc.',\'\'); llamarMetadatos('.$value->Radicado_idRadicado.',\'\'); listarVersiones('.$value->Radicado_idRadicado.');">'.
                            '<span<i class="fa fa-search"></i></span>'.
                        '</a>&nbsp;';
                        
        foreach ($value as $pos => $campo) 
        {
            $row[$key][] = $campo;
        }
        
    }

    $output['aaData'] = $row;
    echo json_encode($output);
?>
