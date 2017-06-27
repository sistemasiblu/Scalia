@extends('layouts.vista')
@section('titulo')<h3 id="titulo"><center></center></h3>@stop

@section('content')
@include('alerts.request')

<script src="http://code.highcharts.com/stock/highstock.js"></script>
<div id='form-section' >

<fieldset id="agendagantt-form-fieldset">  
    <div id="agendagantt" style="height: 500px"></div>
</fieldset>
</div>
<?php
$query = DB::Select('
            SELECT 
                idAgenda,
                nombreCategoriaAgenda,
                asuntoAgenda,
                fechaHoraInicioAgenda,
                fechaHoraFinAgenda
            FROM
                agenda a
                    LEFT JOIN 
                categoriaagenda ca ON a.CategoriaAgenda_idCategoriaAgenda = ca.idCategoriaAgenda
            WHERE a.Compania_idCompania = '.\Session::get('idCompania'));

    $labels = '';
    $categories = '';
    $max = count($query) - 1;

    for ($i=0; $i < count($query); $i++) 
    { 
        $agenda = get_object_vars($query[$i]);


        $inicio = date("Y-m-d H:m:s",substr($agenda['fechaHoraInicioAgenda'], 0, -3));
        $fin = date("Y-m-d H:m:s",substr($agenda['fechaHoraFinAgenda'], 0, -3));

        $finicio = substr($inicio, 0, -9);
        $ffin = substr($fin, 0, -9);

        $finicio = date("Y-m-d", strtotime("-1 MONTH", strtotime($finicio)));
        
        $ffin  = date("Y-m-d", strtotime("-1 MONTH", strtotime($ffin)));

        $agenda['fechaHoraInicioAgenda'] = str_replace('-', ',', $finicio);
        $agenda['fechaHoraFinAgenda'] = str_replace('-', ',', $ffin);

        $labels .= "{
            name: '".$agenda['asuntoAgenda']."',
            intervals: [{ 
                from: Date.UTC(".$agenda['fechaHoraInicioAgenda']."),
                to: Date.UTC(".$agenda['fechaHoraFinAgenda']."),
                label: '".$agenda['asuntoAgenda']."',
                    tooltip_data: '".$agenda['nombreCategoriaAgenda']."'
            }]
        },";
    }

    for ($i=$max; $i >= 0; $i--) 
    { 
        $agenda = get_object_vars($query[$i]);
        $categories .= "'".$agenda['asuntoAgenda']."',";
    }

    $labels = substr($labels, 0, -1);
    $categories = substr($categories, 0, -1);

    echo 
        '<script>
            $(function () {
            var tasks = 
            [
                '.$labels.' 
            ];

            var series = [];
            $.each(tasks.reverse(), function(i, task) {
                var item = {
                    name: task.name,
                    data: []
                };
                $.each(task.intervals, function(j, interval) {
                    item.data.push({
                        x: interval.from,
                        y: i,
                        label: interval.label,
                        from: interval.from,
                        to: interval.to,
                            tooltip_data: interval.tooltip_data
                            
                    }, {
                        x: interval.to,
                        y: i,
                        from: interval.from,
                        to: interval.to,
                            tooltip_data: interval.tooltip_data
                    });
                    
                    if (task.intervals[j + 1]) {
                        item.data.push(
                            [(interval.to + task.intervals[j + 1].from) / 2, null]
                        );
                    }

                });

                series.push(item);

            });

                var chart = new Highcharts.Chart({
                chart: {
                    renderTo: "agendagantt"
                },

                title: {
                    text: "Diagrama de la Agenda"
                },

                xAxis: {
                    type: "datetime"
                },

                yAxis: {
                        min:0,
                        max:'.$max.',
                    categories: ['.$categories.'],
                tickInterval: 1,            
                tickPixelInterval: 200,
                labels: {
                    style: {
                        color: "#525151",
                        font: "12px Helvetica",
                        fontWeight: "bold"
                    },
                },
                startOnTick: false,
                endOnTick: false,
                title: {
                    text: "Eventos"
                },
                minPadding: 0.2,
                maxPadding: 0.2,
                   fontSize:"15px"
                
            },

            legend: {
                enabled: false
            },
            tooltip: {
                formatter: function() {
                    return "<b>"+ tasks[this.y].name + "</b><br/>"+this.point.options.tooltip_data +"<br>" +
                        Highcharts.dateFormat("%d-%m-%Y", this.point.options.from)  +
                        " - " + Highcharts.dateFormat("%d-%m-%Y", this.point.options.to); 
                }
            },

            plotOptions: {
                line: {
                    lineWidth: 10,
                    marker: {
                        enabled: false
                    },
                    dataLabels: {
                        enabled: true,
                        align: "left",
                        formatter: function() {
                            return this.point.options && this.point.options.label;
                        }
                    }
                }
            },

            series: series

            });      
          
          console.log(series);
          });
    </script>';
?>
@stop