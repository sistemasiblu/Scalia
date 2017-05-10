function llamarDescripcionActivo(valor, idCampo) 
{

var token = document.getElementById('token').value;
$.ajax(
{
    

    headers: {'X-CSRF-TOKEN': token},
    dataType: "json",
    url:'/llamarDescripcionActivo',
    data:{codigoActivo: valor},
    type:  'get',
    beforeSend: function()
    {
       
    },
    success: function(data)
    {
      /*  if (#codigoActivo.value == null || ){
                               alert (' vacio');
                           }*/
var codigocampo=document.getElementById('codigoActivo').value;
    
   alert(codigocampo);

                             
        var num = idCampo.replace('codigoActivo', '');
        // alert(data);
        $("#codigoActivo"+num ).val(data[0]['codigoActivo']);
        $("#serieActivo"+num ).val(data[0]['serieActivo']);
        $("#nombreActivo"+num ).val(data[0]['nombreActivo']);
        /*caracteristicaactivo.borrarTodosCampos();
        for (var i = 0;  i <= data.length; i++) 
        {
            caracteristicaactivo.agregarCampos(JSON.stringify(data[i]),'L');
        }
        alert(data);*/
    },
    error:    function(xhr,err)
    {
        alert('Se ha producido un error: ' +err);
    }
});

}

function calcularTotales()
{

     var suma=0;
        var cont=0;
        for (var i = 0; i < movimiento.contador; i++) 
        {
            if($("#cantidadMovimientoActivoDetalle"+i).val())
            {
                suma+= parseFloat($("#cantidadMovimientoActivoDetalle"+i).val());
                cont++;
            }

        }
       
        $("#totalUnidadesMovimientoActivo").val(suma);
        $("#totalArticulosMovimientoActivo").val(cont);
}

function imprimirFormato(idMov, idDoc)
{
   
       
   alert("entra a function imprimir formato");
    window.open('movimientoactivo/'+idMov+'?idTransaccionActivo='+idDoc+'&accion=imprimir','movimientoactivo','width=5000,height=5000,scrollbars=yes, status=0, toolbar=0, location=0, menubar=0, directories=0');

 
}





    function  abrirAprobacionActivo(id)
{
//alert(id);
   var token = document.getElementById('token').value;
          $.ajax(
          {
              headers: {'X-CSRF-TOKEN': token},
              dataType: "json",
              url:'/AprobacionActivos',
              data:{idMovimientoActivo: id},
              type:  'get',
              beforeSend: function(){
              },

              success: function(data)
              {
               
             /* var datos=data;
              alert(datos.lenght);*/
              
                    var html= "<table id='Activos' class='display table-bordered' width='75%'>";
                        html+="<tr class='btn-primary active'>";
                            html+="<td>Id</td>";

                            html+="<td>Codigo</td>";
                            html+="<td>Serial</td>";
                            html+="<td>Descripcion</td>";
                            html+="<td>Cantidad</td>";
                            html+="<td>Observacion</td>";
                            html+="<td>Estado</td>";
                            html+="<td>Motivo Rechazo</td>";
                        html+="</tr>";

                 var idMovActivoD=Array();
                   for (var i = 0; i < data.length; i++) 
                {

                 var idMovActivo=JSON.stringify(data[i]['MovimientoActivo_idMovimientoActivo']);
                 idMovActivoD.push(JSON.stringify(data[i]['idMovimientoActivoDetalle']));
                 var idActivo=JSON.stringify(data[i]['idActivo']);
                 var nombreActivo1=JSON.stringify(data[i]['nombreActivo']);
                 var serieActivo1=JSON.stringify(data[i]['serieActivo']);
                        html+="<tr>";
                            html+="<td><input readonly='readonly' style='border:0px;width:20px;' value='"+JSON.stringify(data[i]['idMovimientoActivoDetalle']).replace(/"/g,"")+"' id='idMovimientoActivoDetalle"+i+"'/></td>";
                            html+="<td><input readonly='readonly' style='border:0px; width:60px;' value='"+JSON.stringify(data[i]['codigoActivo']).replace(/"/g,"")+"' id='codigoActivo"+i+"'/></td>";
                            html+="<td><input readonly='readonly' style='border:0px; width:80px;' value='"+JSON.stringify(data[i]['serieActivo']).replace(/"/g,"")+"' id='serieActivo"+i+"'/></td>";
                            html+="<td><input readonly='readonly' style='border:0px; width:250px;' value='"+JSON.stringify(data[i]['nombreActivo']).replace(/"/g,"")+"' id='nombreActivo"+i+"' style='cursor:pointer;' onclick='VerificacionComponentes("+idActivo+","+nombreActivo1+","+serieActivo1+");'/></td>";
                            html+="<td><input readonly='readonly' style='border:0px; width:40px;'/></td>";
                            html+="<td><input readonly='readonly' style='border:0px;width:60px%;'/></td>";
                            html+="<td ><select required id='EstadoMovimientoActivo"+i+"' style=' border:0px;width:100px;'>";
                                html+="<option value='Aprobado'>Aprobado</option>";
                                html+="<option value='Rechazado'>Rechazado</option>";
                            html+="</select></td>";

                            html+="<td ><select required id='RechazoActivo_idRechazoActivo"+i+"' style='border:0px; width:100%;'>";
                                html+="<option value=''>Seleccione</option>";
                                html+="<option value='1'>No recibido</option>";
                                html+="<option value='2'>Averiado</option>";
                                html+="<option value='3'>Componentes Incompletos</option>";
                                html+="<option value='4'>Componente Trocado</option>";

                            html+="</select></td>";
                            //alert(idMovActivoD);

                            /*html+="<td ><select id='RechazoActivo_idRechazoActivo"+i+"' style='width:100%;'>";
                              html+="<option value=''>Seleccione</option>";
                           
                              for (var i = 0; i < data[1].length; i++) 
                              {
                                var idRechazo= JSON.stringify(data[1][i]['idRechazoActivo']).replace(/"/g,"");
                                var nombreRechazo= JSON.stringify(data[1][i]['nombreRechazoActivo']).replace(/"/g,"");
                                html+="<option value="+idRechazo+">"+nombreRechazo+"</option>";
                              }
                            html+="</select></td>";*/
                            }
                        html+="</tr>";
                      
                    html+="</table><br>";
                    
                    html+="</div>";
                        html+="Observaciones:<BR><textarea id='ObservacionMovimientoActivo' style='width:70%;height:30%;class:ckeditor;' ></textarea>";
                    html+="</div><br><br>";


                    html+="<div>";
                        html+="<button id='botonActivo2'  onclick='ActualizarMovimientoActivo("+data.length+","+idActivo+","+idMovActivo+");' name='botonActivo2' type='submit' class='btn btn-primary'>";
                            html+="Guardar";
                        html+="</button>";
                    html+="</div>";


               


                $("#ContenidoAprobacionActivos").html(html);
                $('#ModalAprobacionActivo').modal('show');


       
                        
                         
                      },
                      error:    function(xhr,err)
                      {
                          alert('Se ha producido un error: ' +err);
                      }
                  });


 
}//fin function abrirAprobacionActivo




function VerificacionComponentes(idActivo,nombreActivo,serialActivo)
{
  //replace(/\D/g,'') quita las letras de una cadena 

   /* var idcampo=JSON.stringify(campo).replace(/\D/g,'');
    var idActivo=$("#idActivo"+idcampo).val();
    var nombreActivo=$("#nombreActivo"+idcampo).val();
    var serialActivo=$("#serieActivo"+idcampo).val();*/


    //alert(idActivo);
          var token = document.getElementById('token').value;
          $.ajax(
          {
              headers: {'X-CSRF-TOKEN': token},
              dataType: "json",
              url:'/VerificacionComponentes',
              data:{idActivo: idActivo},
              type:  'get',
              beforeSend: function(){
              },

              success: function(data)
              {
                console.log(data);
                if (data.length>0) 
                {
                    
                
                

                  //var idActivo=JSON.stringify(data[i]['idActivo']);
                  var html= "<table class='display table-bordered' width='40%'>";
                      html+="<tr><b>"+nombreActivo+" "+serialActivo+"</b></tr>";
                      html+="<tr class='btn-primary active'>";
                          html+="<td>Codigo</td>";
                          html+="<td>Serial</td>";
                          html+="<td>Descripcion</td>";
                          html+="<td>Cantidad</td>";
                      html+="</tr>";
                      for (var i = 0; i < data.length; i++) 
                {
                      html+="<tr>";
                          html+="<td id='codigoActivo"+i+"'>"+JSON.stringify(data[i]['codigoActivo']).replace(/"/g,"")+"</td>";
                          html+="<td>"+JSON.stringify(data[i]['serieActivo']).replace(/"/g,"")+"</td>";
                          html+="<td id='nombreActivo"+i+"' >"+JSON.stringify(data[i]['nombreActivo']).replace(/"/g,"")+"</td>";
                    

                  /*html+="<div>";
                      html+="<button id='cerrar' onclick='this.close();' name='cerrar' type='button' class='btn btn-primary'>";
                          html+="Cerrar";
                      html+="</button>";
                  html+="</div>";*/


                }

                      html+="<td></td>";
                      html+="</tr>";
                  html+="</table><br>";

                  
                $("#ContenidoVerificacionComponentes").html(html);


              }

              else
              {
                  alert("Este Activo no Posee Componentes");

              }
                

               

              

            
              },
              error:    function(xhr,err)
              {
                  alert('Se ha producido un error: ' +err);
              }
          });


   



}



    function cambiarEstado(id, TipoEstado, modificar, eliminar, consultar, aprobar)
{

    location.href= 'http://'+location.host+"/movimientoactivo?idTransaccionActivo="+id+"&TipoEstado="+TipoEstado+"&modificar="+modificar+"&eliminar="+eliminar+"&consultar="+consultar+"&aprobar="+aprobar;
}


function ActualizarMovimientoActivo(contador,idAct,idMov)
    {

      //alert(idMovD);
        //alert(idMovD);
                //alert(id);

      var valor=Array();
      for (var s = 0; s < contador; s++) 
      {
        //alert('EstadoMovimientoActivo'+i);
        //var s=document.getElementById("EstadoMovimientoActivo"+i).value;
        //alert(i);


       



        var id=$("#idMovimientoActivoDetalle"+s).val();
        var RechazoActivo= $("#RechazoActivo_idRechazoActivo"+s).val();
        var EstadoActivo= $("#EstadoMovimientoActivo"+s).val();
        //var idMovD=$("#idMovimientoActivoDetalle"+s).val();
       

        valor.push([idAct,EstadoActivo,RechazoActivo,idMov,id]);
      


      }
    
  
         console.log(valor);

        $.ajax(
        {

        headers: {'X-CSRF-TOKEN': token},
        dataType: "json",
        url:'/ActualizarMovimientoActivo',
        data:{valores: valor},
        type:  'get',
        beforeSend: function()
        {

        },
        success: function(data)
        {
  
        //alert(data);
        },
        error:    function(xhr,err)
        {
            alert('Se ha producido un error: ' +err);
        }
        });

        //$('#ModalAprobacionActivo').modal('close');



    }




