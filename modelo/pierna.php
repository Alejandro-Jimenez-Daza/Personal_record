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
        $squat = filter_var($_POST["squat"], FILTER_SANITIZE_STRING);
        $hamstring = filter_var($_POST["hamstring"], FILTER_SANITIZE_STRING);
        $walking_lunges = filter_var($_POST["walking_lunges"], FILTER_SANITIZE_STRING);
        $calf_raises = filter_var($_POST["calf_raises"], FILTER_SANITIZE_STRING);
        $other_calf_raises = filter_var($_POST["other_calf_raises"], FILTER_SANITIZE_STRING);
        $fecha = filter_var($_POST["fecha"], FILTER_SANITIZE_STRING);

        /** ID FIJO PORQUE SOLO SOY YO */
        $usuario_id = 1;

        // Preparar la consulta SQL con placeholders
        $stmt = $conn->prepare("INSERT INTO pierna (usuario_id, fecha, squat, hamstring, walking_lunges, calf_raises, other_calf_raises) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $usuario_id, $fecha, $squat, $hamstring, $walking_lunges, $calf_raises, $other_calf_raises);

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
    $sql = "SELECT fecha, squat, hamstring, walking_lunges, calf_raises, other_calf_raises FROM pierna WHERE usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Arreglos para almacenar datos
    $squat_data = [];
    $hamstring_data = [];
    $walking_lunges_data = [];
    $calf_raises_data = [];
    $other_calf_raises_data = [];
    $dates = [];

    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['fecha'];
        // Convertir de VARCHAR a FLOAT
        $squat_data[] = floatval($row['squat']);
        $hamstring_data[] = floatval($row['hamstring']);
        $walking_lunges_data[] = floatval($row['walking_lunges']);
        $calf_raises_data[] = floatval($row['calf_raises']);
        $other_calf_raises_data[] = floatval($row['other_calf_raises']);
    }

    // Codificar datos en JSON para usar en JavaScript
    $dates_json = json_encode($dates);
    $squat_data_json = json_encode($squat_data);
    $hamstring_data_json = json_encode($hamstring_data);
    $walking_lunges_data_json = json_encode($walking_lunges_data);
    $calf_raises_data_json = json_encode($calf_raises_data);
    $other_calf_raises_data_json = json_encode($other_calf_raises_data);

    $stmt->close();
    $conn->close();
    ?>

    <div class="container pt-3" id="general">
        <div class="row">
            <div class="container col-md-4" id="data">
                <!-- CONTAINER DE INSERCION DE DATOS -->
                <form method="post" action="pierna.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="squat">Squats</label>
                            <input type="text" class="form-control" id="squat" name="squat" required>
                        </div>
                        <div class="form-group">
                            <label for="hamstring">Hamstring</label>
                            <input type="text" class="form-control" id="hamstring" name="hamstring" required>
                        </div>
                        <div class="form-group">
                            <label for="walking_lunges">Walking Lunges</label>
                            <input type="text" class="form-control" id="walking_lunges" name="walking_lunges" required>
                        </div>
                        <div class="form-group">
                            <label for="calf_raises">Calf Raises</label>
                            <input type="text" class="form-control" id="calf_raises" name="calf_raises" required>
                        </div>
                        <div class="form-group">
                            <label for="other_calf_raises">Other Calf Raises</label>
                            <input type="text" class="form-control" id="other_calf_raises" name="other_calf_raises" required>
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
                            <th scope="col">Squat</th>
                            <th scope="col">Hamstring</th>
                            <th scope="col">Walking Lunges</th>
                            <th scope="col">Calf Raises</th>
                            <th scope="col">Other Calf Raises</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reabrir la conexión a la base de datos
                        include '../controlador/conexion.php';
                        
                        $sql = "SELECT fecha, squat, hamstring, walking_lunges, calf_raises, other_calf_raises FROM pierna WHERE usuario_id = ?";
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
                                echo "<td>{$row['squat']}</td>";
                                echo "<td>{$row['hamstring']}</td>";
                                echo "<td>{$row['walking_lunges']}</td>";
                                echo "<td>{$row['calf_raises']}</td>";
                                echo "<td>{$row['other_calf_raises']}</td>";
                                echo "</tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center'>No se encontraron registros</td></tr>";
                        }
                        $stmt->close();
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CONTENEDOR PARA EL GRÁFICO -->
    <div class="container pt-3">
        <h2 class="text-center">Progreso del entrenamiento</h2>
        <button id="toggleChartBtn" class="btn btn-success mb-3">Mostrar/Ocultar Gráfico</button>
        <canvas id="progressChart"></canvas>
    </div>

    <script>
        // Obtener datos de PHP
        const dates = <?php echo $dates_json; ?>;
        const squatData = <?php echo $squat_data_json; ?>;
        const hamstringData = <?php echo $hamstring_data_json; ?>;
        const walkingLungesData = <?php echo $walking_lunges_data_json; ?>;
        const calfRaisesData = <?php echo $calf_raises_data_json; ?>;
        const otherCalfRaisesData = <?php echo $other_calf_raises_data_json; ?>;

        // Crear gráfico con Chart.js
        const ctx = document.getElementById('progressChart').getContext('2d');
        const progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Squats',
                        data: squatData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Hamstring',
                        data: hamstringData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Walking Lunges',
                        data: walkingLungesData,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Calf Raises',
                        data: calfRaisesData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    },
                    {
                        label: 'Other Calf Raises',
                        data: otherCalfRaisesData,
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
        const chartContainer = document.querySelector('.container.pt-3 canvas');
        const toggleChartBtn = document.getElementById('toggleChartBtn');

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
