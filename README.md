# Nodos

Nuestra URL de Acceso a nuestra Pagina WEB es:

http://142.44.246.66/

URL API get que entrega la informacion registrada en nuestro Nodo.

http://142.44.246.66/suma

Entregando la siguiente informacion:  {"total":0,"ip":"142.44.246.66"}

*************************** Montaje En Un Nuevo Proyecto (NODO) ************************************************

Para el montaje de nuestro proyecto en un nuevo nodo o maquina se deben realizar los siguientes pasos.

En una terminal ejecutamos el siguiente comando:

composer install

**** Solo para la primera vez que entra al proyecto en un pc nuevo ***** 

Luego ejecutamos los siguientes comandos.

php artisan key:generate

php artisan cache:clear

Crear una tabla en la base llamada nodos

En laravel en el archivo .env configurar credenciales de la db.

La configuracion de este proyecto es la siguiente,  vale recordar que cada configuracion depende del MYSQL que tiene cada PC.

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=nodos

DB_USERNAME=nodos

DB_PASSWORD=Nodos2020*

Luego correr el siguiente comando

php artisan migrate

**** OJO desde el directorio raiz del proyecto  (c:xampp/htdocs/nodos) ********

********************* FORMA DE EJECUCION DEL PROGRAMA MODELO VISTA CONTROLADOR *****************************

MODELO

Ruta= app/Numero.php

Ruta= app/Server.php

VISTA

Ruta = resources/index.blade.php

CONTROLADOR

Ruta = app/Http/NumerosController.php

ROUTE 
Aquí es donde puede registrar rutas web de nuestra aplicación.

Route::get('/', 'NumerosController@index');

Route::post('/guardarNumero', 'NumerosController@guardarNumeroWeb');

Route::post('/guardarURL', 'NumerosController@guardarURL');

Route::post('/borraURL/{id}/', [
    'as' => 'borraURL', 'uses' => 'NumerosController@borrarURL']);
    
Route::get('/suma', 'NumerosController@retornaSumaNumeros');

Route::post('/llamarASuma', 'NumerosController@LlamarServidoresYSumar');


**************************************** BASE DE DATOS *****************************************************

Manejamos dos Tablas una para guardar el listado de numeros y otra para almacenar el listado de servidores.

TABLA NUMERO

id 

numero

nombre

created_at

updated_at


TABLA SERVERS

id

ip

url

YaloConsulte

YameConsulto

created_at

updated_at 

**************************************************************************************************************
METODOS UTILIZADOS 
---------------------------------------------------------------------------------------------------
Metodo que utilizamos para Mostrar los numeros que se ingresaron por los usuarios

Public Function index ()
---------------------------------------------------------------------------------------------------

// Metodo que utilizamos para guardar los numeros que seran ingresados por los usuarios

Public function guardarNumeroWeb()
---------------------------------------------------------------------------------------------------

// Metodo que utilizamos cuando nos consultan los valores.

public function retornaSumaNumeros()

//JSON QUE ENTREGA LA INFORMACION DE LA SUMA DE LOS VALORES ENTREGADOS Y LA IP QUE LO IDENTIFICA

return response()->json(['total' => $suma,'ip' => $_SERVER['REMOTE_ADDR'] ]);
---------------------------------------------------------------------------------------------------

//Cuando yo consulto a los demas.

public function LlamarServidoresYSumar().

// Metodo que almacen la URL recibida en la base de datos y los imprime en pantalla.

public function guardarURL()

----------------------------------------------------------------------------------------------------

Para restablecer las bases de datos utilizamos el comando 

php artisan migrate:refresh
 
