<?php

namespace App\Http\Controllers;

use App\Numero;
use App\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Ixudra\Curl\Facades\Curl;


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
        foreach ($listadoServidores as $servidor) {
            if($servidor->yaLoConsulte == 1)
                print("Servidor Consultado Anteriormente -> ");
            //Me guardo el servidor como consultado.
            $servidor->yaLoConsulte = 1;
            $servidor->save();
           // JSON QUE RECIBE LA INFORMACION DE LAS URL ALMACENADAS
            $response = Http::get($servidor->url)['total'];
            $total += floatval ($response);
            print("Respuesta Servidor ".$response."<br>");
        }

        //Falta sumarme a mi mismo
        $listadoNumeros = Numero::all();
        $suma = 0;
        foreach ($listadoNumeros as $n) {
            $suma += $n->numero;
        }
        print("<br>");
        print("Valor Sin Sumar el Local: ".$total."<br>");
        print("Valor Local: ".$suma."<br>");
        print("<br>");
        print("Valor Total Final: ".($total+$suma));
    }

    // Metodo que almacen la URL recibida en la base de datos y los imprime en pantalla.
    public function guardarURL(Request $request){

        $urlAAgregar = $request->input('url');

        //toca Recorrer todos los servidores y buscar si ya exite antes de agregar
        $listadoServidores = Server::all();
        $existe = false;
        foreach ($listadoServidores as $servidor) {
            if($servidor->url == $urlAAgregar){
                $existe = true;
            }
        }

        if(!$existe){
            //guardar si no existe
            $nuevoServer = new Server();
            $nuevoServer->url = $request->input('url');
            $nuevoServer->save();
        }else{
            print("Ya existe el servidor, no se agrega");
        }

        foreach ($listadoServidores as $servidor) {
            if('http://'.$_SERVER['REMOTE_ADDR'].'/nodos/public/' != $servidor->url){


                if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $servidor->url, $ip_match)) {
                    $ip = $ip_match[0];
                }
                $response = Curl::to($servidor->url.'guardarNuevoNodo/'.$ip.'/')
                ->get();
                dd($response);
            }
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

        dd("OK");
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
