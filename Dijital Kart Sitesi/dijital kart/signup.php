<?php
include 'db.php'; // Veritabanı bağlantı dosyasını dahil ediyoruz

// Eğer form POST yöntemiyle gönderildiyse işlemleri başlat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];   // Formdan gelen kullanıcı adını al
    $email = $_POST['email'];         // Formdan gelen e-posta adresini al
    $password = $_POST['password'];   // Formdan gelen şifreyi al
    $password2 = $_POST['password2']; // Formdan gelen şifre tekrarını al

    // Şifreler aynı mı kontrol et
    if ($password !== $password2) {
        $message = "Şifreler uyuşmuyor!"; // Eğer şifreler farklıysa hata mesajı ata
    } else {
        // Şifreyi güvenli şekilde hash'le
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Kullanıcıyı veritabanına eklemek için SQL sorgusu hazırla
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql); // Hazır sorgu oluştur
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password); // Değerleri bağla

        // Sorguyu çalıştır
        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php"); // Kayıt başarılıysa login.php'ye yönlendir
            exit; // Yönlendirmeden sonra kodun devam etmesini engelle
        } else {
            $message = "Hata: " . mysqli_error($conn); // Hata olursa mesaj ata
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            transition: 0.3s;
        }

        /* Gece Modu Stilleri */
        body.dark-mode {
            background-color: #222;
            color: white;
        }

        body.dark-mode .signup-container {
            background: #333;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
        }

        body.dark-mode input[type="text"],
        body.dark-mode input[type="password"],
        body.dark-mode input[type="email"] {
            background-color: #444;
            color: white;
            border-color: #555;
        }

        body.dark-mode .signup-btn {
            background-color: #444;
        }

        body.dark-mode .signup-btn:hover {
            background-color: #555;
        }

        body.dark-mode .login-link {
            color: #66b3ff;
        }

        .signup-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #222;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .signup-btn {
            width: 100%;
            padding: 10px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .signup-btn:hover {
            background: #444;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Tema Değiştirme Butonu */
        .theme-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: #333;
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        body.dark-mode .theme-btn {
            color: white;
            background: rgba(45, 45, 45, 0.8);
        }
    </style>
</head>
<body>
    <button class="theme-btn" onclick="toggleTheme()">🌓</button>

    <div class="signup-container">
        <h1>Kayıt Ol</h1>
        <form action="signup_process.php" method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">E-posta:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Şifre Tekrar:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="signup-btn">Kayıt Ol</button>
        </form>
        <div class="login-link">
            Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a>
        </div>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        }

        // Sayfa yüklendiğinde temayı kontrol et ve uygula
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>