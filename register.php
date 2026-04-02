<?php
// register.php

session_start();
require_once 'models/Visitor.php';
require_once 'functions.php';

$visitorModel = new Visitor();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($name)) {
        $error = 'Введите ФИО';
    } elseif (empty($password)) {
        $error = 'Введите пароль';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif ($visitorModel->findByName($name)) {
        $error = 'Гость с таким именем уже существует';
    } else {
        $visitorId = $visitorModel->create($name, password_hash($password, PASSWORD_DEFAULT), $email, $phone);
        if ($visitorId) {
            $_SESSION['user_id'] = $visitorId;
            $_SESSION['username'] = $name;
            header('Location: index.php');
            exit;
        }
        $error = 'Ошибка при регистрации';
    }
}
?>
<?php include 'header.php'; ?>

<main>
    <div class="container py-5" style="max-width: 700px;">
        <a href="index.php" class="btn btn-link text-decoration-none mb-4">
            <i class="bi bi-arrow-left"></i> Вернуться на главную
        </a>
        
        <div class="card border-0 shadow-lg rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">📝 Регистрация гостя</h2>
                    <p class="text-secondary">Создайте аккаунт для бронирования номеров</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">ФИО гостя *</label>
                        <input type="text" name="name" class="form-control rounded-pill py-2 px-3" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control rounded-pill py-2 px-3" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Телефон</label>
                            <input type="tel" name="phone" class="form-control rounded-pill py-2 px-3" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Пароль *</label>
                        <input type="password" name="password" class="form-control rounded-pill py-2 px-3" required>
                        <div class="form-text">Минимум 6 символов</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Подтверждение пароля *</label>
                        <input type="password" name="confirm_password" class="form-control rounded-pill py-2 px-3" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold">
                        <i class="bi bi-person-plus-fill"></i> Зарегистрироваться
                    </button>
                    
                    <div class="text-center mt-4 pt-2 border-top">
                        <span class="text-secondary">Уже есть аккаунт?</span>
                        <a href="#" id="showLoginModal" class="text-decoration-none fw-bold">Войти</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
document.getElementById('showLoginModal')?.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('authModal').style.display = 'flex';
});

const password = document.getElementById('password');
const confirm = document.getElementById('confirm_password');

function checkMatch() {
    if (confirm.value.length > 0) {
        if (password.value !== confirm.value) {
            confirm.classList.add('is-invalid');
            confirm.classList.remove('is-valid');
        } else {
            confirm.classList.add('is-valid');
            confirm.classList.remove('is-invalid');
        }
    }
}

password?.addEventListener('keyup', checkMatch);
confirm?.addEventListener('keyup', checkMatch);
</script>
</body>
</html>