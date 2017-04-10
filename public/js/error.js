$(document).ready(function(){
    $("#ocultar").click(function(event){
         event.preventDefault();
        $("#oculto").hide("slow");
     });

    $("#mostrar").click(function(event){
        event.preventDefault();
        $("#oculto").show(1000);
    });
});
