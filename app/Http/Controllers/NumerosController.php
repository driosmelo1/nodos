<?php

namespace App\Http\Controllers;

use App\Numero;
use App\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;

// Metodo que utilizamos para Mostrar los numeros que se ingresaron por los usuarios

class NumerosController extends Controller
{
    public function index()
    {
        $listadoServidores = Server::all();

        $mostrarNumeroNodo = false;
        return view('index')
            ->with('listadoServidores', $listadoServidores)
            ->with('mostrarNumeroNodo',$mostrarNumeroNodo);
    }


    // Metodo que utilizamos para guardar los numeros que seran ingresados por los usuarios

    public function guardarNumeroWeb(Request $request){

        $numeroNuevo = new Numero();
        $numeroNuevo->nodo = $request->input('nodoSeleccionado');
        $numeroNuevo->numero = $request->input('numero');
        $numeroNuevo->save();

        $listadoServidores = Server::all();
        $mostrarNumeroNodo = false;
        return redirect('/')
            ->with('listadoServidores',$listadoServidores)
            ->with('mostrarNumeroNodo',$mostrarNumeroNodo);
    }

    public function ConsultarNodo(Request $request){
        $NodosASumar = Numero::where('nodo', $request->input('nodoAConsultar'))->get();
        $suma = 0;
        foreach ($NodosASumar as $nodo) {
            $suma += $nodo->numero;
        }
        $listadoServidores = Server::all();
        $mostrarNumeroNodo = true;
        return view('index')
            ->with('listadoServidores',$listadoServidores)
            ->with('NodoAMostrar',$request->input('nodoAConsultar'))
            ->with('suma',$suma)
            ->with('mostrarNumeroNodo',$mostrarNumeroNodo);
    }
    public function ConsultarNodoAPI(Request $request){
        $NodosASumar = Numero::where('nodo', $request->input('nodo'))->get();
        $suma = 0;
        foreach ($NodosASumar as $nodo) {
            $suma += $nodo->numero;
        }
        $listadoServidores = Server::all();
        $mostrarNumeroNodo = true;
        return response()->json(['suma' => $suma]);
    }

    // Metodo que utilizamos cuando nos consultan los valores

    public function retornaSumaNumeros(Request $request){

        //Leer quien me llamo, y guardar

        $listadoNumeros = Numero::all();

        $suma = 0;
        foreach ($listadoNumeros as $n) {
            $suma += $n->numero;
        }

        $listadoServidores = Server::all();
        //Saber la ip del server remoto
        $ipCliente = $_SERVER['REMOTE_ADDR'];

        // Recorrido de validacion de consulta de servidores.
        foreach ($listadoServidores as $servidor) {
            if(strpos($servidor->url, $ipCliente) !== false){
                $servidor->yaMeConsulto = 1;
                $servidor->save();
                print("NO encontrado, marcando leido");
            }else{

            }
        }
        //esto es para saber la ip de internet
        /*
            $externalContent = file_get_contents('http://checkip.dyndns.com/');
            preg_match('/Current IP Address: \[?([:.0-9a-fA-F]+)\]?/', $externalContent, $m);
            $externalIp = $m[1];
        */
        //JSON QUE ENTREGA LA INFORMACION DE LA SUMA DE LOS VALORES ENTREGADOS Y LA IP QUE LO IDENTIFICA
        return response()->json(['total' => $suma,'ip' => $_SERVER['REMOTE_ADDR'] ]);
    }

    //Cuando yo consulto a los demas
    public function LlamarServidoresYSumar(){

        $listadoServidores = Server::all();
        $total = 0;

        for ($i=1; $i < count($listadoServidores)+1 ; $i++) {
            $listadoServidores[$i-1]->consultado = 1;
            $listadoServidores[$i-1]->save();
            $sumatoria[$i] = 0;
            $NodosASumar = Numero::where('nodo', $i)->get();
            foreach ($NodosASumar as $nodo) {
                $sumatoria[$i] += $nodo->numero;
                $total += $nodo->numero;
            }
        }

        /*
        foreach ($listadoServidores as $servidor) {
            if($servidor->yaLoConsulte == 1)
                print("Servidor Consultado Anteriormente -> ");
            //Me guardo el servidor como consultado.
            $servidor->yaLoConsulte = 1;
            $servidor->save();
           // JSON QUE RECIBE LA INFORMACION DE LAS URL ALMACENADAS
            $response = Http::get($servidor->url)['total'];
            $total += floatval ($response);
        }

        //Falta sumarme a mi mismo
        $listadoNumeros = Numero::all();
        $suma = 0;
        foreach ($listadoNumeros as $n) {
            $suma += $n->numero;
        }
        */
        return response()->json(['total' => $total, 'listado por nodo:' => $sumatoria ]);
    }

    // Metodo que almacen la URL recibida en la base de datos y los imprime en pantalla.
    public function guardarURL(Request $request){

        $urlAAgregar = $request->input('url');

        //toca Recorrer todos los servidores y buscar si ya exite antes de agregar
        //guardar si no existe
        $nuevoServer = new Server();
        $nuevoServer->url = $request->input('url');
        $nuevoServer->save();

        $listadoServidores = Server::all();
        foreach ($listadoServidores as $servidor) {
            $nuevoServer2 = new Server();
            $nuevoServer2->setConnection('mysql2');
            $nuevoServer2->url = $request->input('url');
            $nuevoServer2->save();
        }

       //retornar a la pagina con listado completo
        $listadoServidores = Server::all();
        return redirect('/')->with('listadoServidores',$listadoServidores);
    }

    // Metodo que almacen la URL recibida en la base de datos y los imprime en pantalla.
    public function guardarNuevoNodo(string $ip){

        $url = 'http://'.$ip.'/nodos/public/';
        //guardar si no existe
        $nuevoServer = new Server();
        $nuevoServer->url = $url;
        $nuevoServer->save();

    }

    // Funcion que Elimina Las URL almacenadas en nuestra base de datos, Como Vecinos.
    //Retorna en Pantalla Las URl de Los Servidores Almacenados.

    public function borrarURL(int $id){

        $serverABorrar = Server::find($id);

        $serverABorrar->delete();

        $listadoServidores = Server::all();
        return redirect('/')->with('listadoServidores',$listadoServidores);
    }
}
