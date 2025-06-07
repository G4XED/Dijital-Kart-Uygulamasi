<?php
session_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Kart</title>
    <style>
        /* Genel Stiller */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            transition: 0.3s;
        }

        /* Gece Modu Stilleri */
        body.dark-mode {
            background-color: #222;
            color: white;
        }

        /* Menü Stilleri */
        .menu {
            background: #333;
            padding: 1rem;
            position: sticky;
            top: 0;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            align-items: center;
        }

        .menu ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
        }

        .menu a:hover {
            color:#969696;
            padding: 2px 4px;
            transition: 0.3s;
            border-radius: 4px;
        }

        /* Tema Değiştirme Butonu */
        .theme-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
        }
        

        /* İçerik Stilleri */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: calc(100vh - 140px); /* Footer'ı alta sabitlemek için */
        }

        /* Footer Stilleri */
        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 1rem;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        /* Responsive Tasarım */
        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                align-items: flex-start;
            }

            .menu ul {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                width: 100%;
            }

            .right-links {
                align-items: flex-end;
            }
            .content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Menü -->
    <nav class="menu">
    <ul>
        <li><a href="index.php">Ana Sayfa</a></li>
        <li><a href="hakkimizda.php">Hakkımızda</a></li>
        <li><a href="iletisim.php">İletişim</a></li>
    </ul>
    <ul class="right-links">
        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="kartolustur.php">Kart Oluştur</a></li>
            <li style="color:white; font-weight:bold;">
                <?php echo htmlspecialchars($_SESSION['username']); /* Eğer kullanıcı giriş yaptıysa*/ ?>
            </li>
            <li><a href="logout.php">Çıkış Yap</a></li>
        <?php else: ?>
            <li><a style="text-align: right;" href="login.php">Giriş Yap</a></li>
            <li><a href="signup.php">Kayıt Ol</a></li>
        <?php endif; ?>
        <li><button class="theme-btn" onclick="toggleTheme()">🌓</button></li>
    </ul>
</nav>

    <!-- İçerik -->
    <div class="content">
        <h1>Bize Ulaşın</h1>
        <p>Adres: </p>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d240371.48831684465!2d28.84737385567028!3d41.00520413844495!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caa7040068086b%3A0xe1ccfe98bc01b0d0!2zxLBzdGFuYnVs!5e1!3m2!1str!2str!4v1749143417218!5m2!1str!2str" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <h1>İletişim</h1>
        <p>Tel No: 0511 111 1111 <br>
            Mail: dijitalkartsitesi@gmail.com</p>
    </div>
    

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>

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