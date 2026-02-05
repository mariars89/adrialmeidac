<?php
// Comprobar que cURL esté habilitado
ini_set('display_errors', 0);
error_reporting(0);

if (!function_exists('curl_init')) {
    return('La extensión cURL no está habilitada en el servidor.');
}


// Función para obtener la configuración de conexión
function getConexion() {
    return [
        'endpoint' => 'get_producto_trozos',
        'token'    => 'abc123xyz',
        'base_url' => 'http://10.10.1.168:3000'
    ];
}
 
// Función genérica para ejecutar cURL
function ejecutarCurl($url) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    return [
        'response' => $response,
        'error' => $error,
        'httpCode' => $httpCode
    ];
}


// Función de búsqueda de productos
function buscarProducto($bsq){
    $conexion = getConexion(); // Obtener endpoint, token y base_url

    $url_bsq = $conexion['base_url'] . "/" . $conexion['endpoint'] . "/" . rawurlencode($bsq) . "?token=" . $conexion['token'];

    $resultado = ejecutarCurl($url_bsq);
//Control de errores
           if ($resultado['response'] === false) {
        return "Error de conexión: {$resultado['error']}";
    }

    if ($resultado['httpCode'] >= 400) {
        return "Error HTTP: {$resultado['httpCode']}";
    }

    $aDatos = json_decode($resultado['response'], true);

    if (!isset($aDatos['products']) || empty($aDatos['products'])) {
        return "No se encontraron productos para $bsq";
    }

    // Devolver array de productos
    return $aDatos['products'];
}
?>
