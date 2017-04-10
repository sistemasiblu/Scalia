function abreMenu(numeroMenu, total)
{
	total = Math.ceil(total/4) * 100;

	
	$('#arrow'+numeroMenu).slideToggle('slow');
	$('#gridbox'+numeroMenu).slideToggle('slow',function(){
	$('ul').animate({marginTop:'0'});
	$('#gridbox'+numeroMenu).animate({height: total+'px'});
	});

	if ($("#estadomenu").val() == 1)
	{
		cierraMenuImportacion(21);	
	}
	
	event.stopPropagation();
}


function cierraMenu(numeroMenu) 
{
	
	$('#arrow'+numeroMenu).hide();
	$('#gridbox'+numeroMenu).hide();
	$('ul').animate({marginTop:'0'});
	$('#gridbox'+numeroMenu).animate();
}

function abreMenuImportacion(estado, numeroMenu, total)
{
	if (estado == 0) 
	{	
		total = Math.ceil(total/4) * 200;

		heightimp = $('li#'+numeroMenu).height();
		topimp = $('li#'+numeroMenu).position().top + 120;
		leftimp = $('li#'+numeroMenu).position().left + 30;

		toptot = parseFloat(topimp) + parseFloat(heightimp);

		$('#gridboximportacion'+numeroMenu).css("top", topimp);
		$('#gridboximportacion'+numeroMenu).css("left", leftimp);

		$('#arrow'+numeroMenu).slideToggle('slow');
		$('#gridboximportacion'+numeroMenu).slideToggle('slow',function(){
		$('ul').animate({marginTop:'0'});
		$('#gridboximportacion'+numeroMenu).animate({height: total+'px'});
		});
		$("#estadomenu").val(1);
	}
	else
	{
		$('#arrow'+numeroMenu).hide();
		$('#gridboximportacion'+numeroMenu).hide();
		$('ul').animate({marginTop:'0'});
		$('#gridboximportacion'+numeroMenu).animate();

		$("#estadomenu").val(0);
	}
	
}

function cierraMenuImportacion(numeroMenu)
{
	$('#arrow'+numeroMenu).hide();
	$('#gridboximportacion'+numeroMenu).hide();
	$('ul').animate({marginTop:'0'});
	$('#gridboximportacion'+numeroMenu).animate();

	$("#estadomenu").val(0);
}

$(document).ready(function(){

	$('#menuarch').click(function(event){
		$('#arrowarch').slideToggle('slow');
		$('#gridboxarch').slideToggle('slow',function(){
			$('ul').animate({marginTop:'0'});
			$('#gridboxarch').animate();
		});
			event.stopPropagation();
	});
	
	$('#menudoc').click(function(event){
		$('#arrowdoc').slideToggle('slow');
		$("#gridboxdoc").css("position", "absolute"); 

		$('#gridboxdoc').slideToggle('slow',function(){
			//$('ul').animate({marginTop:'0'});
			$('#gridboxdoc').animate();
		});
			event.stopPropagation();
	});
	
	$('#menuseg').click(function(event){
		$('#arrowseg').slideToggle('slow');
		$("#gridboxseg").css("position", "absolute"); 

		$('#gridboxseg').slideToggle('slow',function(){
			//$('ul').animate({marginTop:'0'});
			$('#gridboxseg').animate({height:'200px'});
		});
			event.stopPropagation();
	});
	
	$('body').click(function() {
	
		$('#arrowarch').hide();
		$('#gridboxarch').hide();
		$('ul').animate({marginTop:'0'});
		$('#gridboxarch').animate();

		$('#arrowdoc').hide();
		$('#gridboxdoc').hide();
		$('ul').animate({marginTop:'0'});
		$('#gridboxdoc').animate();
		
		$('#arrowseg').hide();
		$('#gridboxseg').hide();
		$('ul').animate({marginTop:'0'});
		$('#gridboxseg').animate({height:'200px'});		
	});

});