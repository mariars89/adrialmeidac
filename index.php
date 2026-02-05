<?php
//activar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/prueba/peticion_curl.php';

// Capturar el valor del input
$bsq_input = $_GET['bsq'] ?? null; 

// Variable para almacenar resultados o mensaje de error
$resultadoProductos = null;
$errorMensaje = null;

if (!empty($bsq_input)) {
    $respuesta = buscarProducto($bsq_input);
    if (is_array($respuesta)) {
        $resultadoProductos = $respuesta;
    } else {
        $errorMensaje = $respuesta; // Es un mensaje de error si el producto no existe.
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- link para Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Buscador</title>
    <style>
        /* Efecto de cursor de mano al pasar sobre una fila */
        tbody tr:hover {
            cursor: pointer;
        }
    </style>
</head>
<body class="container mt-5">
    <h1 class="text-left">Buscador de productos</h1>
    
    <!-- Contenedor centrado con borde -->
    <form method="get" class="mb-4">
        <div class="input-group border d-flex align-items-center justify-content-center p-3">
            <input type="text" name="bsq" class="form-control border-end-0" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($bsq_input); ?>">
            <button type="submit" class="btn btn-primary border-start-0">Buscar</button>
        </div>
    </form>
    <h3 class="text-left">Realiza una búsqueda para ver resultados</h3>
    
    <!-- Resultados de la búsqueda -->     
    <div class="productosEncontrados">
        <?php
        if ($errorMensaje) {
            echo "<div class='alert alert-danger mt-4' role='alert'>$errorMensaje</div>";
        } elseif ($resultadoProductos) {
            $totalResultados = count($resultadoProductos);
            echo "<h2>PETICIÓN REALIZADA: $bsq_input</h2>";
            echo "<p class='text-muted border d-flex align-items-center justify-content-start p-1'>Filtrar resultados...</p>";
            echo "<p class='text-muted'>Mostrando $totalResultados de $totalResultados resultados</p>";
            echo "<table class='table table-bordered table-sm' style='border-color: #dee2e6'>";
            echo "<thead><tr><th>Producto</th><th>Código de barras</th></tr></thead><tbody>";
            foreach ($resultadoProductos as $aProducto) {
                echo "<tr class='barcode-row'>
                        <td>".$aProducto['Name']."</td>
                        <td class='barcode'>".$aProducto['BarcodeSummary']."</td>
                      </tr>";
            }
            echo "</tbody></table>";
        }
        ?>
    </div>
    
    <p class="text-center mt-4 mb-0 text-muted">Ayuda: Haz doble clic en cualquier fila para copiar el código de barras al portapapeles.</p>

    <!--Bootstrap Bundle with Popper-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    
    <script>
        // Validación de campo de búsqueda
        let form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            let input = document.querySelector('input[name="bsq"]');
            if (input.value.trim() === '') {
                e.preventDefault();
                alert('Debes rellenar el campo de busqueda');
                input.focus();
            }            
        });

        // Funcionalidad de copiar al portapapeles al hacer doble clic en una fila
        document.querySelectorAll('.barcode-row').forEach(row => {
            row.addEventListener('dblclick', function() {
                // Seleccionamos el texto del código de barras
                let barcode = this.querySelector('.barcode').textContent;
                
                // Crear un elemento temporal para copiar al portapapeles
                let tempInput = document.createElement('input');
                document.body.appendChild(tempInput);
                tempInput.value = barcode;
                tempInput.select();
                document.execCommand('copy');  // Copiar al portapapeles
                document.body.removeChild(tempInput);  // Eliminar el input temporal
                alert('Código de barras copiado: ' + barcode); // Mensaje de confirmación
            });
        });
    </script>
</body>
</html>


