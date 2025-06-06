<?php
session_start();
$errors = [
    'login' => $_SESSION['login_error'] ?? null,
    'register' => $_SESSION['register_error'] ?? null,
];
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError($error)
{
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}
function isActiveForm($formName, $activeForm)
{
    return $formName === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina de Login</title>
    <link rel="stylesheet" href="style-login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<body>
    <div class="container">
        <div class="form-box <?php echo isActiveForm('login', $activeForm); ?>" id="login-form">
            <form action="login_register.php" method="POST">
                <h2>Login</h2>
                <?php echo showError($errors['login']); ?>
                <input type="email" name="email" placeholder="Correo Electrónico" required autocomplete="email">
                <input type="password" name="password" placeholder="Contraseña" required
                    autocomplete="current-password">

                <button type="submit" name="login"> Login </button>
                <p>No tienes una cuenta?<a href="#" onclick="showForm('register-form')">Registrarse</a></p>
            </form>
        </div>

        <div class="form-box <?php echo isActiveForm('register', $activeForm); ?>" id="register-form">
            <form action="login_register.php" method="POST">
                <h2>Registro</h2>
                <?php echo showError($errors['register']); ?>
                <input type="text" name="name" placeholder="Nombre" required autocomplete="name">
                <input type="email" name="email" placeholder="Correo Electrónico" required autocomplete="email">
                <input type="password" name="password" placeholder="Contraseña" required autocomplete="new-password">
                <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" require
                    autocomplete="confirm_password">
                <button type="submit" name="register"> Registrarse </button>
                <p>Ya tienes una cuenta?<a href="#" onclick="showForm('login-form')">Login</a></p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>