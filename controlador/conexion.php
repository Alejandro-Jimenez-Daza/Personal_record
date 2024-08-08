<?php

// Crear conexión
$conn = new mysqli("localhost", "root", "", "sss");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>