<?php
session_start();

// Veritabanı bağlantısını kur
// Bilgileri kendi veritabanı ayarlarınıza göre güncelleyin (host, dbname, user, password)
$db = new PDO("mysql:host=localhost;dbname=dijitalkart;charset=utf8", "root", "");

// En son oluşturulan kartın ID'sini veritabanından al (Eğer varsa, 'Kartı Görüntüle' butonu için)
$sql_last_card = "SELECT id FROM digital_cards ORDER BY id DESC LIMIT 1";
$stmt_last_card = $db->query($sql_last_card);
$last_card = $stmt_last_card->fetch(PDO::FETCH_ASSOC);
$last_card_id = $last_card ? $last_card['id'] : null;

// Form POST metodu ile gönderildiğinde bu kısım çalışır
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // *** Kullanıcının form alanlarına girdiği bilgileri alıyoruz ***
    $name = $_POST['name']; // 'Adınız Soyadınız' alanından gelen bilgi
    $bio = $_POST['bio']; // 'Biyografi' alanından gelen bilgi
    $email = $_POST['email']; // 'E-posta' alanından gelen bilgi
    $phone = $_POST['phone']; // 'Telefon' alanından gelen bilgi
    $avatar = $_POST['avatar']; // Seçilen avatarın numarası
    
    // Sosyal medya linklerini bir dizide topluyoruz
    // Her bir sosyal medya alanı için, eğer formdan bilgi geldiyse onu al, gelmediyse boş bırak
    $social_media_data = [
        'instagram' => isset($_POST['instagram']) ? $_POST['instagram'] : '', 
        'twitter' => isset($_POST['twitter']) ? $_POST['twitter'] : '', 
        'linkedin' => isset($_POST['linkedin']) ? $_POST['linkedin'] : '', 
        'github' => isset($_POST['github']) ? $_POST['github'] : '' 
    ];
    
    // Sosyal medya bilgilerini, veritabanına daha kolay kaydedebilmek için özel bir metin formatına (JSON) çevir
    $social_media_json = json_encode($social_media_data);
    
    // Projeler ve yetenekler için girilen metinleri al
    $projects_text = $_POST['projects'];
    $skills_text = $_POST['skills'];

    // *** Alınan bilgileri veritabanına kaydetme işlemi ***

    // Veritabanına yeni bir dijital kart kaydı eklemek için güvenli bir sorgu hazırla
    // Sorgudaki '?' işaretleri yerine daha sonra formdan aldığımız bilgileri koyacağız (SQL Injection'ı önlemek için)
    $sql_insert = "INSERT INTO digital_cards (name, bio, email, phone, social_media, projects, skills, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $db->prepare($sql_insert);
    
    // Hazırlanan sorguyu formdan aldığımız bilgilerle çalıştır
    $stmt_insert->execute([$name, $bio, $email, $phone, $social_media_json, $projects_text, $skills_text, $avatar]);

    // Veritabanına en son eklenen kaydın (yani yeni oluşturduğumuz kartın) kendi numarasını (ID) al
    $card_id = $db->lastInsertId();

    // *** Kart başarıyla oluşturuldu, kullanıcıyı kartını görebileceği sayfaya yönlendir ***
    // Yönlendirme adresi: 'kart.php' sayfası, yeni oluşturulan kartın ID'si ile
    header("Location: kart.php?id=" . $card_id . "&creator=true"); // '&creator=true' bilgisi kart sayfasında bazı şeyleri göstermek için kullanılabilir (ör: linki kopyalama mesajı)
    exit(); // Yönlendirme komutundan sonra başka kod çalışmasın diye burada durdur
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dijital Kart Oluştur</title>
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

        body.dark-mode input[type="text"],
        body.dark-mode input[type="email"],
        body.dark-mode input[type="tel"],
        body.dark-mode textarea {
            background-color: #444;
            color: white;
            border-color: #555;
        }

        body.dark-mode .success-message {
            background-color: #2d4a2d;
            color: #fff;
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
            background-color: gray;
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
        
        button {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 100px;
        }

        /* Footer Stilleri */
        .footer {
            background:#333;
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

        /* Form stilleri */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 10px 0;
            border: 3px solid #ddd;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .button-group button {
            flex: 1;
        }

        .view-button {
            background-color: #28a745;
        }

        .view-button:hover {
            background-color: #218838;
        }

        /* Avatar seçim stilleri */
        .avatar-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
            justify-content: center;
        }

        .avatar-option {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid #ddd;
            transition: all 0.3s;
        }

        .avatar-option:hover {
            transform: scale(1.1);
            border-color: #007bff;
        }

        .avatar-option.selected {
            border-color: #28a745;
            transform: scale(1.1);
        }

        input[type="radio"] {
            display: none;
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
        <h1>Kendi Dijital Kartınızı Oluşturun</h1>
        
        <form method="POST" action="">
            <!-- Temel Bilgiler -->
            <div class="form-group">
                <label>Adınız Soyadınız:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>Biyografi:</label>
                <textarea name="bio" rows="4"></textarea>
            </div>

            <!-- Avatar Seçimi -->
            <div class="form-group">
                <label>Profil Fotoğrafı Seçin:</label>
                <div class="avatar-options">
                    <label>
                        <input type="radio" name="avatar" value="1" required>
                        <img src="images/normalbeyaz.png" alt="Avatar 1" class="avatar-option">
                    </label>
                    <label>
                        <input type="radio" name="avatar" value="2">
                        <img src="images/normalsiyah.png" alt="Avatar 2" class="avatar-option">
                    </label>
                    <label>
                        <input type="radio" name="avatar" value="3">
                        <img src="images/normalkadinbeyaz.png" alt="Avatar 3" class="avatar-option">
                    </label>
                    <label>
                        <input type="radio" name="avatar" value="4">
                        <img src="images/normalkadinbeyaz2.png" alt="Avatar 4" class="avatar-option">
                    </label>
                    <label>
                        <input type="radio" name="avatar" value="5">
                        <img src="images/normalkadinbeyaz3.png" alt="Avatar 5" class="avatar-option">
                    </label>
                </div>
            </div>

            <!-- İletişim Bilgileri -->
            <h3>İletişim Bilgileri</h3>
            <div class="form-group">
                <label>E-posta:</label>
                <input type="email" name="email">
            </div>

            <div class="form-group">
                <label>Telefon:</label>
                <input type="tel" name="phone">
            </div>

            <!-- Sosyal Medya -->
            <h3>Sosyal Medya Hesaplarınız</h3>
            <div class="form-group">
                <label>Instagram:</label>
                <input type="text" name="instagram">
            </div>

            <div class="form-group">
                <label>Twitter:</label>
                <input type="text" name="twitter">
            </div>

            <div class="form-group">
                <label>LinkedIn:</label>
                <input type="text" name="linkedin">
            </div>

            <div class="form-group">
                <label>GitHub:</label>
                <input type="text" name="github">
            </div>

            <!-- Projeler -->
            <h3>Projeleriniz</h3>
            <div class="form-group">
                <label>Projeleriniz (Her satıra bir tane yazabilirsiniz):</label>
                <textarea name="projects" rows="6"></textarea>
            </div>

            <!-- Yetenekler -->
            <h3>Yetkinlikleriniz</h3>
            <div class="form-group">
                <label>Yetkinlikleriniz (Virgülle ayırarak yazabilirsiniz):</label>
                <textarea name="skills" rows="6"></textarea>
            </div>

            <div class="button-group">
                <button type="submit">Kartı Oluştur</button>
                <?php if ($last_card_id): ?>
                    <a href="kart.php?id=<?php echo $last_card_id; ?>">
                        <button type="button" class="view-button">Kartı Görüntüle</button>
                    </a>
                <?php endif; ?>
            </div>
        </form>
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

            // Avatar seçimi için event listener'ları ekle
            const avatarOptions = document.querySelectorAll('.avatar-option');
            avatarOptions.forEach(avatar => {
                avatar.addEventListener('click', function() {
                    // Önce tüm avatar seçimlerini kaldır
                    avatarOptions.forEach(opt => opt.classList.remove('selected'));
                    // Tıklanan avatarı seçili yap
                    this.classList.add('selected');
                });
            });
        });
    </script>
</body>
</html>