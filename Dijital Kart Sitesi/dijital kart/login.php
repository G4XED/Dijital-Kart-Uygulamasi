<?php
include 'db.php'; // Veritabanı bağlantı dosyasını dahil ediyoruz

// Eğer form POST yöntemiyle gönderildiyse işlemleri başlat
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];   // Formdan gelen kullanıcı adını al
    $password = $_POST['password'];   // Formdan gelen şifreyi al

    // Kullanıcıyı veritabanında aramak için SQL sorgusu hazırla
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql); // Hazır sorgu oluştur
    mysqli_stmt_bind_param($stmt, "s", $username); // Kullanıcı adını sorguya bağla
    mysqli_stmt_execute($stmt); // Sorguyu çalıştır
    $result = mysqli_stmt_get_result($stmt); // Sonuçları al

    // Eğer kullanıcı bulunduysa
    if ($row = mysqli_fetch_assoc($result)) {
        // Girilen şifre ile veritabanındaki hash'li şifreyi karşılaştır
        if (password_verify($password, $row['password'])) {
            session_start(); // Oturumu başlat
            $_SESSION['username'] = $username; // Kullanıcı adını oturuma kaydet
            header('Location: index.php'); // Giriş başarılıysa ana sayfaya yönlendir
        } else {
            echo "Şifre yanlış!"; // Şifre uyuşmazsa hata mesajı göster
        }
    } else {
        echo "Kullanıcı bulunamadı!"; // Kullanıcı yoksa hata mesajı göster
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
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

        body.dark-mode .login-container {
            background: #333;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
        }

        body.dark-mode input[type="text"],
        body.dark-mode input[type="password"] {
            background-color: #444;
            color: white;
            border-color: #555;
        }

        body.dark-mode .login-btn {
            background-color: #444;
        }

        body.dark-mode .login-btn:hover {
            background-color: #555;
        }

        body.dark-mode .signup-link {
            color: #66b3ff;
        }

        .login-container {
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
        input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-btn:hover {
            background: #444;
        }

        .signup-link {
            text-align: center;
            margin-top: 15px;
        }

        .signup-link a {
            color: #007bff;
            text-decoration: none;
        }

        .signup-link a:hover {
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

    <div class="login-container">
        <h1>Giriş Yap</h1>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Giriş Yap</button>
        </form>
        <div class="signup-link">
            Hesabınız yok mu? <a href="signup.php">Kayıt Ol</a>
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