<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ejercicios</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h1 class="text-center pt-3">Ingresa los registros en KG</h1>

    <?php
    // Incluir la conexión a la base de datos
    include '../controlador/conexion.php';

    // Manejo del formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validar y sanitizar inputs
        $pull_ups = filter_var($_POST["pull_ups"], FILTER_SANITIZE_STRING);
        $cable_row = filter_var($_POST["cable_row"], FILTER_SANITIZE_STRING);
        $fecha = filter_var($_POST["fecha"], FILTER_SANITIZE_STRING);

        /** ID FIJO PORQUE SOLO SOY YO */
        $usuario_id = 1;

        // Preparar la consulta SQL con placeholders
        $stmt = $conn->prepare("INSERT INTO espalda (usuario_id, fecha, pull_ups, cable_row) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $fecha, $pull_ups, $cable_row);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<div class='alert alert-success' role='alert'>Registro guardado correctamente</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Error al guardar el registro: " . $stmt->error . "</div>";
        }

        // Cerrar el statement
        $stmt->close();
    }

    // Consulta para obtener los datos
    $usuario_id = 1; // Asegúrate de que este valor coincida con el valor usado en la inserción
    $sql = "SELECT fecha, pull_ups, cable_row FROM espalda WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Arreglos para almacenar datos
    $pull_ups_data = [];
    $cable_row_data = [];
    $dates = [];

    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['fecha'];
        // Convertir de VARCHAR a FLOAT
        $pull_ups_data[] = floatval($row['pull_ups']);
        $cable_row_data[] = floatval($row['cable_row']);
    }

    // Codificar datos en JSON para usar en JavaScript
    $dates_json = json_encode($dates);
    $pull_ups_data_json = json_encode($pull_ups_data);
    $cable_row_data_json = json_encode($cable_row_data);

    $stmt->close();
    $conn->close();
    ?>

    <div class="container pt-3" id="general">
        <div class="row">
            <div class="container col-md-4" id="data">
                <!-- CONTAINER DE INSERCION DE DATOS -->
                <form method="post" action="espalda.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pull_ups">Weighted Pull ups</label>
                            <input type="text" class="form-control" id="pull_ups" name="pull_ups" required>
                        </div>
                        <div class="form-group">
                            <label for="cable_row">Cable Rows</label>
                            <input type="text" class="form-control" id="cable_row" name="cable_row" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group pb-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                    <a href="../home.php" class="btn btn-warning">Regresar</a>
                </form>
            </div>

            <!-- CONTAINER DE LA TABLA -->
            <div class="container col-md-8" id="table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Pull ups</th>
                            <th scope="col">Cable Rows</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reabrir la conexión a la base de datos
                        include '../controlador/conexion.php';
                        
                        $sql = "SELECT fecha, pull_ups, cable_row FROM espalda WHERE usuario_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $usuario_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Verificar resultados
                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<th scope='row'>{$count}</th>";
                                echo "<td>{$row['fecha']}</td>";
                                echo "<td>{$row['pull_ups']}</td>";
                                echo "<td>{$row['cable_row']}</td>";
                                echo "</tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No se encontraron registros</td></tr>";
                        }
                        $stmt->close();
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container pt-3">
        <button id="toggleChart" class="btn btn-success">Mostrar/Ocultar Gráfico</button>
    </div>

    <!-- CONTENEDOR PARA EL GRÁFICO -->
    <div class="container pt-3" id="chartContainer">
        <h2 class="text-center">Progreso de Ejercicios</h2>
        <canvas id="progressChart"></canvas>
    </div>

    <script>
        // Obtener datos de PHP
        const dates = <?php echo $dates_json; ?>;
        const pullUpsData = <?php echo $pull_ups_data_json; ?>;
        const cableRowData = <?php echo $cable_row_data_json; ?>;

        // Crear gráfico con Chart.js
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Pull Ups',
                        data: pullUpsData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Cable Rows',
                        data: cableRowData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Peso (KG)'
                        }
                    }
                }
            }
        });

        // Controlar la visibilidad del gráfico
        const chartContainer = document.getElementById('chartContainer');
        const toggleChartBtn = document.getElementById('toggleChart');

        // Inicialmente ocultar el gráfico
        chartContainer.style.display = 'none';

        toggleChartBtn.addEventListener('click', () => {
            if (chartContainer.style.display === 'none') {
                chartContainer.style.display = 'block';
            } else {
                chartContainer.style.display = 'none';
            }
        });
    </script>
</body>

</html>
