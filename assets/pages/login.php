<?php
// Inclui a conexão com o banco de dados
include '../includes/database.php';
session_start();

$database = new Database();
$conn = $database->getConnection();
$error_message = "";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta ao banco de dados para verificar as credenciais
    $query = "SELECT id, username FROM usuarios WHERE username = :username AND password = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password); // Idealmente, a senha deve ser criptografada (hash)

    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Se as credenciais estiverem corretas, inicia uma sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redireciona para a página inicial
        header("Location: ../../index.php");
        exit();
    } else {
        $error_message = "Credenciais inválidas!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gerenciamento de UAN</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- Adicionando Bootstrap ao projeto -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<header>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
            <div class="container">
            <!-- Logo do sistema -->
            <a class="navbar-brand text-white" href="#">ZAYON - Sistema de Gerenciamento de UAN</a>
            </div>
</header>
<body>
    <main>
        <section class="form-section">
        <h2>Login</h2>
        <form method="POST" action="">
            <div class="input-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Senha: </label>
                <input type="password" id="password" name="password" required>
            </div><br>
            <button type="submit">Entrar</button>
        </form>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 ZAYON - Todos os direitos reservados.</p>
    </footer>
</body>
</html>
