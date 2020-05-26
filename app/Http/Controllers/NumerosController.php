<?php

namespace App\Http\Controllers;

use App\Numero;
use App\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


// Metodo que utilizamos para Mostrar los numeros que se ingresaron por los usuarios

class NumerosController extends Controller
{
    public function index()
    {
        $listadoNumeros = Numero::all();

        $suma = 0;
        foreach ($listadoNumeros as $n) {
            $suma += $n->numero;
        }

        $listadoServidores = Server::all();
        return view('index')->with('listadoNumeros', $listadoNumeros)->with('listadoServidores', $listadoServidores)->with('suma',$suma);
    }


    // Metodo que utilizamos para guardar los numeros que seran ingresados por los usuarios

    public function guardarNumeroWeb(Request $request){

        $numeroNuevo = new Numero();

        $numeroNuevo->numero = $request->input('numero');
        $numeroNuevo->save();

        $listadoNumeros = Numero::all();
        $listadoServidores = Server::all();
        return redirect('/')->with('listadoNumeros',$listadoNumeros)->with('listadoServidores',$listadoServidores);
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

        $nuevoServer = new Server();

        $nuevoServer->url = $request->input('url');
        $nuevoServer->save();

        $listadoNumeros = Numero::all();
        $listadoServidores = Server::all();
        return redirect('/')->with('listadoNumeros',$listadoNumeros)->with('listadoServidores',$listadoServidores);
    }
    // Funcion que Elimina Las URL almacenadas en nuestra base de datos, Como Vecinos.
    //Retorna en Pantalla Las URl de Los Servidores Almacenados.

    public function borrarURL(int $id){

        $serverABorrar = Server::get($id);

        $serverABorrar->delete();

        $listadoNumeros = Numero::all();
        $listadoServidores = Server::all();
        return redirect('/')->with('listadoNumeros',$listadoNumeros)->with('listadoServidores',$listadoServidores);
    }
}
