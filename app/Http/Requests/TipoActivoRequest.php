<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TipoActivoRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

    //$request = $this->instance()->all();
    //$rules = [];
    //$caracteristicas = $request['nombreTipoActivoCaracteristica'];
    $caracteristica_rules = 'required|max:10';
    //$documentos = $request['descripcionTipoActivoDocumento'];
    $documento_rules = 'required|max:10';


$rules = [
    'CodigoTipoActivo' => 'required|max:5',
  ];

if($this->request->get('nombreTipoActivoCaracteristica')!="")
{
  foreach($this->request->get('nombreTipoActivoCaracteristica') as $key => $val)
  {
    $rules['nombreTipoActivoCaracteristica.'.$key] = 'required|max:10';
  }

}

  return $rules;




       
    }


   public function messages()
{
  $messages = [];
  foreach($this->request->get('nombreTipoActivoCaracteristica') as $key => $val)
  {
    $messages['nombreTipoActivoCaracteristica.'.$key.'.max'] = 'El campo nombre caracterisitica '.$key.' es requerido';
  }
  return $messages;
}

}
