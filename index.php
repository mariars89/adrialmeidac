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
        $errorMensaje = $respuesta;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables Bootstrap CSS -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <title>Buscador</title>

    <style>       
        tbody tr:hover {
            cursor: pointer;
        }
    </style>
</head>

<body class="container mt-4">

<h1 class="text-left">Buscador de productos</h1>

<form method="get" class="mb-4">
    <div class="input-group border rounded-3 d-flex align-items-center justify-content-center p-3">
        <input type="text" name="bsq" class="form-control border rounded-3"
               placeholder="Buscar producto..."
               value="<?php echo htmlspecialchars($bsq_input); ?>"
               id="search-input">
        <button type="submit" class="btn btn-primary ms-2 rounded-3">Buscar</button>
    </div>
</form>

<div class="productosEncontrados">  
<?php 
if ($errorMensaje) {
    echo "<div class='alert alert-warning' role='alert'>$errorMensaje</div>";
} elseif ($resultadoProductos) {

    $totalResultados = count($resultadoProductos);

    echo "<h2>PETICIÓN REALIZADA: $bsq_input</h2>";
    echo "<p class='text-muted'>Mostrando $totalResultados de $totalResultados resultados</p>";

    echo "<table id='tablaProductos' class='table table-bordered table-sm table-striped'>";
    echo "<thead>
            <tr>
                <th>Producto</th>
                <th>Código de barras</th>
            </tr>
          </thead>
          <tbody>";

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

<p class="text-start mt-4 mb-0 text-muted">
    Ayuda: Haz doble clic en cualquier fila para copiar el código de barras al portapapeles.
</p>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
window.onload = function() {
    document.getElementById("search-input").focus();
};

// Validación de campo de búsqueda
let form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    let input = document.querySelector('input[name="bsq"]');
    if (input.value.trim() === '') {
        e.preventDefault();
        alert('Debes rellenar el campo de búsqueda');
        input.focus();
    }
});

// Inicializar DataTable (solo si existe la tabla)
$(document).ready(function () {
    if ($('#tablaProductos').length) {
        $('#tablaProductos').DataTable({
            paging: true,
            searching: false,
            ordering:false,
            pageLength: 10,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
            }
        });
    }
});

// Doble clic para copiar código de barras
document.addEventListener('dblclick', function(e) {
    let row = e.target.closest('.barcode-row');
    if (!row) return;

    let barcode = row.querySelector('.barcode').textContent;

    let tempInput = document.createElement('input');
    document.body.appendChild(tempInput);
    tempInput.value = barcode;
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    alert('Código de barras copiado: ' + barcode);
});
</script>

</body>
</html>



