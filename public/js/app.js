var wrapper = document.getElementById("signature-pad"),
    clearButton = wrapper.querySelector("[data-action=clear]"),
    saveButton = wrapper.querySelector("[data-action=save]"),
    closeButton = wrapper.querySelector("[data-action=close]"),
    closeImageButton = wrapper.querySelector("[data-action=close]"),
    canvas = wrapper.querySelector("canvas"),
    signaturePad;

if(document.getElementById("image-pad"))
{
    var wrapper2 = document.getElementById("image-pad"),
     closeImageButton = wrapper2.querySelector("[data-action=close]");
}
// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio =  Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);
}

function mostrarFirma(reg)
{   
    if(document.getElementById("signature-pad").style.display == "block" || 
        document.getElementById("signature-pad").style.display == "")
        document.getElementById("signature-pad").style.display = "none";
    else
        document.getElementById("signature-pad").style.display = "block";

    if(reg != 'undefined')
        document.getElementById("signature-reg").value = reg;
    else
        document.getElementById("signature-reg").value = '';

    resizeCanvas() ;
}


function mostrarImagen(ruta)
{

    if(document.getElementById("image-pad").style.display == "none" || 
        document.getElementById("image-pad").style.display == "")
        document.getElementById("image-pad").style.display = "none";
    else
        document.getElementById("image-pad").style.display = "none";

    if(ruta != 'undefined')
    {       
        imagen = ruta.substring(ruta.length-4);
        if (imagen == 'null') 
        {
            alert('No hay archivo cargado.');
        } 
        else
        {
            // document.getElementById("image-src").src = ruta;
            window.open(ruta);
        }
    }
    // else
    //     document.getElementById("image-src").src = '';
}

window.onresize = resizeCanvas;
resizeCanvas();

signaturePad = new SignaturePad(canvas);

clearButton.addEventListener("click", function (event) {
    signaturePad.clear();
});

saveButton.addEventListener("click", function (event) {
    if (signaturePad.isEmpty()) {
        alert("Por Favor Registre Su Firma.");
    } else {
        //window.open(signaturePad.toDataURL());
        reg = '';
        if(document.getElementById("signature-reg").value != 'undefined')
            reg = document.getElementById("signature-reg").value;
        

        document.getElementById("firma"+reg).src = signaturePad.toDataURL() ;
        document.getElementById("firmabase64"+reg).value = signaturePad.toDataURL() ;
        mostrarFirma();
    }
});

closeButton.addEventListener("click", function (event) {
    document.getElementById("signature-pad").style.display = "none";
});

closeImageButton.addEventListener("click", function (event) {
    document.getElementById("image-pad").style.display = "none";
});
