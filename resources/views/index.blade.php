<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Nodos</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEo760AUcUmFx3ibVJJAzGytlQcNXd" crossorigin="anonymous"></script>
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

        </style>
    </head>
    <body>
        ip de este nodo:
        {{$_SERVER['REMOTE_ADDR']}}

        <div class="flex-center position-ref full-height">
            <h1 style="text-align: center">NODOS</h1>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        @if (count($listadoServidores) > 0)
                            <hr>
                            <h2>Guardar Numero</h2>
                            <br>
                            {{ Form::open(array('url' => '/guardarNumero')) }}
                                {{Form::label('numero_lbl', 'Ingresa el numero a guardar')}}
                                <br>
                                {{Form::number('numero')}}
                                <br>
                                {{Form::label('servidor_lbl', 'En que nodo se va a guardar')}}
                                <br>
                                <select name="nodoSeleccionado">
                                    @for ($i = 1; $i < count($listadoServidores)+1; $i++)
                                        @if ($i == 1)
                                            <option value="{{$i}}" selected>Nodo {{$i}}</option>
                                        @else
                                            <option value="{{$i}}">Nodo {{$i}}</option>
                                        @endif
                                    @endfor
                                </select>
                                <br>
                                {{Form::submit('Generar')}}
                            {{ Form::close() }}
                            <br>
                            <h2>listado Numeros</h2>
                            <hr>
                            @if ($mostrarNumeroNodo == 1)
                                <p>Esto se muestra si consulto alguna sumatoria de algun nodo<br>
                                    El valor total para el nodo {{$NodoAMostrar}} es : {{$suma}}</p>
                            @else
                                <h3>Selecciona el nodo a consultar</h3>
                                <form method="get" action="/nodos/public/ConsultarNodo" >
                                    <select name="nodoAConsultar">
                                        @for ($i = 1; $i < count($listadoServidores)+1; $i++)
                                            @if ($i == 1)
                                                <option value="{{$i}}" selected>Nodo {{$i}}</option>
                                            @else
                                                <option value="{{$i}}">Nodo {{$i}}</option>
                                            @endif
                                        @endfor
                                    </select>
                                    <input type="submit" value="Consultar">
                                </form>
                            @endif

                        @else
                            <h2>Antes de agregar un numero debes tener al menos este servidor agregado en la lista</h2>
                            {{ Form::open(array('url' => '/guardarURL')) }}
                                    {{Form::label('url_lbl', 'Agregar '.$_SERVER['REMOTE_ADDR']. 'a la lista')}}
                                    <br>
                                    {{Form::text('url','http://'.$_SERVER['REMOTE_ADDR'].'/nodos/public/')}}
                                    <br>

                                    {{Form::submit('Guardase a si mismo')}}
                                {{ Form::close() }}
                        @endif
                    </div>
                    <div class="col-md-6">
                        <hr>
                        <h2>Guardar nuevo servidor o url para consulta de sumas</h2>
                        <br>
                        {{ Form::open(array('url' => '/guardarURL')) }}
                            {{Form::label('url_lbl', 'Ingresa la url del api para retornar suma')}}
                            <br>
                            {{Form::text('url')}}
                            <br>

                            {{Form::submit('Guardar')}}
                        {{ Form::close() }}

                        <br>
                        <h2>listado de servidores</h2>
                        <hr>
                        @if (count($listadoServidores) > 0)
                        Cantidad de Servidores Guardados: {{count($listadoServidores)}}
                        <br>
                            @foreach ($listadoServidores as $servidor)
                            id: {{$servidor->id}} -URL:{{$servidor->url}}
                            <a  class="btn btn-danger" href="{{ url('/borraURL/'.$servidor->id.'/') }}">eliminar</a>
                            <br>
                            @endforeach
                        @endif
                        <h2>solicitar y sumar</h2>
                        <hr>
                        {{ Form::open(array('url' => '/llamarASuma')) }}
                            {{Form::submit('GO')}}
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
