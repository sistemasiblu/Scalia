<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaSeguimiento extends Model
{
    protected $table = 'agendaseguimiento';
    protected $primaryKey = 'idAgendaSeguimiento';

    protected $fillable = ['Agenda_idAgenda', 'fechaHoraAgendaSeguimiento', 'Users_idCrea', 'detallesAgendaSeguimiento'];

    public $timestamps = false;

    public function agenda()
    {
    	return $this->hasOne('App\Agenda','Agenda_idAgenda');
    }

}