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
        $dips = filter_var($_POST["dips"], FILTER_SANITIZE_STRING);
        $incline_press = filter_var($_POST["incline_press"], FILTER_SANITIZE_STRING);
        $fecha = filter_var($_POST["fecha"], FILTER_SANITIZE_STRING);
        
        /** ID FIJO PORQUE SOLO SOY YO */
        $usuario_id = 1;

        // Preparar la consulta SQL con placeholders
        $stmt = $conn->prepare("INSERT INTO pecho (usuario_id, fecha, dips, incline_press) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $fecha, $dips, $incline_press);

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
    $sql = "SELECT fecha, dips, incline_press FROM pecho WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Arreglos para almacenar datos
    $dips_data = [];
    $incline_press_data = [];
    $dates = [];

    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['fecha'];
        // Convertir de VARCHAR a FLOAT
        $dips_data[] = floatval($row['dips']);
        $incline_press_data[] = floatval($row['incline_press']);
    }

    // Codificar datos en JSON para usar en JavaScript
    $dates_json = json_encode($dates);
    $dips_data_json = json_encode($dips_data);
    $incline_press_data_json = json_encode($incline_press_data);

    $stmt->close();
    $conn->close();
    ?>

    <div class="container pt-3" id="general">
        <div class="row">
            <div class="container col-md-4" id="data">
                <!-- CONTAINER DE INSERCION DE DATOS -->
                <form method="post" action="pecho.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="dips">Dips</label>
                            <input type="text" class="form-control" id="dips" name="dips" required>
                        </div>
                        <div class="form-group">
                            <label for="incline_press">Incline Press</label>
                            <input type="text" class="form-control" id="incline_press" name="incline_press" required>
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
                            <th scope="col">Dips</th>
                            <th scope="col">Incline Bench Press</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reabrir la conexión a la base de datos
                        include '../controlador/conexion.php';
                        
                        $sql = "SELECT fecha, dips, incline_press FROM pecho WHERE usuario_id = ?";
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
                                echo "<td>{$row['dips']} KG</td>";
                                echo "<td>{$row['incline_press']} KG</td>";
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

    <!-- Botón para mostrar/ocultar el gráfico -->
    <div class="container pt-3">
        <button id="toggleChart" class="btn btn-success">Mostrar/Ocultar Gráfico</button>
    </div>

    <!-- CONTENEDOR PARA EL GRÁFICO -->
    <div class="container pt-3" id="chartContainer">
        <h2 class="text-center">Progreso del entrenamiento</h2>
        <canvas id="progressChart"></canvas>
    </div>

    <script>
        // Obtener datos de PHP
        const dates = <?php echo $dates_json; ?>;
        const dipsData = <?php echo $dips_data_json; ?>;
        const inclinePressData = <?php echo $incline_press_data_json; ?>;

        // Crear gráfico con Chart.js
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Dips',
                        data: dipsData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Incline Press',
                        data: inclinePressData,
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

        // Funcionalidad para mostrar/ocultar el gráfico
        const toggleButton = document.getElementById('toggleChart');
        const chartContainer = document.getElementById('chartContainer');

        toggleButton.addEventListener('click', () => {
            if (chartContainer.style.display === 'none') {
                chartContainer.style.display = 'block';
                toggleButton.textContent = 'Ocultar Gráfico';
            } else {
                chartContainer.style.display = 'none';
                toggleButton.textContent = 'Mostrar Gráfico';
            }
        });
    </script>
</body>

</html>
