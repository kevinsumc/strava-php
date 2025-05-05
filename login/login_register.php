<?php
session_start();
require_once 'config.php';

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = $connection->query("SELECT * FROM usuarios WHERE email = '$email'");
    if($check_email->num_rows > 0){
        $_SESSION['register_error'] = "El correo ya está registrado";
        $_SESSION['active_form'] = 'register';
    } else {
        $insert = $connection->query("INSERT INTO usuarios (name, email, password) VALUES ('$name', '$email', '$password')");
        if (!$insert) {
            die("Error al registrar: " . $connection->error);
        }
    }

    header("Location: index.php");
    exit();
}

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $connection -> query("SELECT * FROM usuarios WHERE email = '$email'");
    if($result -> num_rows > 0){
        $user = $result -> fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            header("Location: http://localhost:81/strava-php/dashboard.php");
            exit();
        }else{
            $_SESSION['login_error'] = "Contraseña incorrecta";
        }
    }else{
        $_SESSION['login_error'] = "El correo no está registrado";
    }

    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}
?>