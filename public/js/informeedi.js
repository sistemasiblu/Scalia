
function consultarRotacionEDI(idVentaEDI, idInventarioEDI, Marca_idMarca, 
                        TipoProducto_idTipoProducto, Categoria_idCategoria, 
                        EsquemaProducto_idEsquemaProducto, TipoNegocio_idTipoNegocio, 
                        Temporada_idTemporada, grupo
)
{
    var condicionRot = '';
    condicionRot = condicionRot + 
                    ((condicionRot != '' && idVentaEDI != 0) ? ' and ' : '') + 
                    (idVentaEDI != 0 ? 'VentaEDI_idVentaEDI = '+idVentaEDI : '');

    condicionRot = condicionRot + 
                    ((condicionRot != '' && idInventarioEDI != 0) ? ' and ' : '') + 
                    (idInventarioEDI != 0 ? 'InventarioEDI_idInventarioEDI = '+idInventarioEDI : '');

    var condicionDatos = '';
    condicionDatos = condicionDatos + 
                    ((condicionDatos != '' && Marca_idMarca != 0) ? ' and ' : '') + 
                    (Marca_idMarca != 0 ? 'Marca_idMarca = '+Marca_idMarca : '');

    condicionDatos = condicionDatos + 
                    ((condicionDatos != '' && TipoProducto_idTipoProducto != 0) ? ' and ' : '') + 
                    (TipoProducto_idTipoProducto != 0 ? 'TipoProducto_idTipoProducto = '+TipoProducto_idTipoProducto : '');
    
    condicionDatos = condicionDatos + 
                    ((condicionDatos != '' && Categoria_idCategoria != 0) ? ' and ' : '') + 
                    (Categoria_idCategoria != 0 ? 'Categoria.codigoAlterno1Categoria like "'+Categoria_idCategoria+'%"' : '');

    condicionDatos = condicionDatos + 
                    ((condicionDatos != '' && EsquemaProducto_idEsquemaProducto != 0) ? ' and ' : '') + 
                    (EsquemaProducto_idEsquemaProducto != 0 ? 'EsquemaProducto_idEsquemaProducto = '+EsquemaProducto_idEsquemaProducto : '');

    condicionDatos = condicionDatos + 
                    ((condicionDatos != '' && TipoNegocio_idTipoNegocio != 0) ? ' and ' : '') + 
                    (TipoNegocio_idTipoNegocio != 0 ? 'TipoNegocio_idTipoNegocio = '+TipoNegocio_idTipoNegocio : '');

    condicionDatos = condicionDatos + 
                    ((condicionDatos != '' && Temporada_idTemporada != 0) ? ' and ' : '') + 
                    (Temporada_idTemporada != 0 ? 'Temporada_idTemporada = '+Temporada_idTemporada : '');


    window.open('consultaVentaInventario/'+
                    '?condicionRot='+condicionRot+
                    '&condicionDatos='+condicionDatos+
                    '&grupo='+grupo
                    ,'_blank','width=2500px, height=700px, scrollbars=yes');

}
