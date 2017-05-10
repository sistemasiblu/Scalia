<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\tipoaccionRequest;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;

class TipoAccionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('tipoacciongrid');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tipoaccion');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(tipoaccionRequest $request)
    {
        \App\TipoAccion::create(
        [
        'codigoTipoAccion'=>$request['codigoTipoAccion'],
        'nombreTipoAccion'=>$request['nombreTipoAccion']

        ]);
        return Redirect('/tipoaccion');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipoaccion=\App\TipoAccion::find($id);
        return view('tipoaccion',['tipoaccion'=>$tipoaccion]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(tipoaccionRequest $request, $id)
    {
        $tipoaccion=\App\TipoAccion::find($id);
        $tipoaccion->fill($request->all());
        $tipoaccion->save();

        return redirect('\tipoaccion');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \App\TipoAccion::destroy($id);
        return redirect('\tipoaccion');
    }
}
