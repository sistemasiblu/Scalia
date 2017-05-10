
<center>
@include('alerts.suceso');
</center>
    <div id="contenedor">
        

            {!! Form::open(['route' => 'mail.store', 'method'=>'POST'])!!} 
             {!!Form::text('nombres',null,["required"=>"required",'class'=> 'form-control','id'=>'nombres','placeholder'=>'Digite su Nombre'])!!}

            {!!Form::email('email',null,["required"=>"required",'class'=> 'form-control','id'=>'email','placeholder'=>'Digite su correo'])!!}
               
                
                {!!Form::text('comentarios', null,["required"=>"required",'class'=> 'form-control','id'=>'comentarios','placeholder' => 'Digite sus comentarios'])!!}

                
               
              
                {!! Form::submit('ENVIAR') !!}
            {!!Form::close()!!}
        
        
     
    </div>





       
