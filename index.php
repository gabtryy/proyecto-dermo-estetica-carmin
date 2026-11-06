<?php //etiqueta de apertura para codigo php
//ARCHIVO index.php 
//Se conoce como "From controller"
//es el encargado de direccionar todo el flujo de las
//paginas en nuetro sitio web


$pagina = !empty($_GET['pagina']) ? $_GET['pagina'] : 'home';
require_once 'modelo/verifica.php';
$verifica = new verifica();

if ($pagina === 'salida') {
   $verifica->destruyesesion();
}

$nivel = $verifica->leesesion();
if ($nivel === '') {
   $pagina = 'login';
} elseif ($pagina === 'login') {
   $pagina = 'home';
}
 
 //pregunta si existe el archivo
 //si existe lo trae, si no escribe pagina en costrucción
 if(is_file("controlador/".$pagina.".php")){ //verifica que exista dentro
 //de la carpeta controlador un archivo php que se llame como el valor de $pagina
    //si lo encuentra lo trae, el no va, el se trae el archivo
	//esta es la diferencia con mvc, tu no vas, tu te traes a ti lo que necesitas
	//el siguiente archivo que debe habrir el estudiante es por ejemplo controlador/principal.php
	//y ver el contenido
    require_once("controlador/".$pagina.".php");
 }
 else{
    echo "PAGINA EN CONSTRUCCIÓN";
 }
 
?>
