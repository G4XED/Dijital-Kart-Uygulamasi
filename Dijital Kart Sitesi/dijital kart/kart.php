<?php
// Oturumu başlat (kullanıcının giriş bilgilerine erişmek için)
session_start();

// Veritabanı bağlantısını kur
// Bilgileri kendi veritabanı ayarlarınıza göre güncelleyin (host, dbname, user, password)
$db = new PDO("mysql:host=localhost;dbname=dijitalkart;charset=utf8", "root", "");

// *** URL'den gerekli bilgileri alıyoruz ***

// URL'de 'id' adında bir parametre var mı kontrol et
// Varsa bu değeri al, yoksa varsayılan olarak 1 kullan (veya 0, geçersiz ID durumunda)
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; // (int) ile alınan değerin sayı olduğundan emin oluyoruz

// Eğer ID geçersizse (0 veya negatifse) veya kart bulunamazsa diye sonra kontrol edeceğiz.

// URL'de 'creator' adında bir parametre var mı ve değeri 'true' mu kontrol et
// Bu bilgi, kartı oluşturan kişinin görüntülediğini varsaymak için kullanılabilir (ama yetki kontrolü için veritabanı bilgisi daha güvenlidir)
$is_creator_param = isset($_GET['creator']) && $_GET['creator'] === 'true';

// *** Veritabanından kart bilgilerini çekme ***

// Belirtilen ID'ye sahip dijital kartın tüm bilgilerini veritabanından çekmek için güvenli bir sorgu hazırla
// Sorgudaki '?' işareti yerine URL'den aldığımız ID değerini koyacağız
$sql_card = "SELECT * FROM digital_cards WHERE id = ?";
$stmt_card = $db->prepare($sql_card);

// Sorguyu ID değeri ile çalıştır
$stmt_card->execute([$id]);

// Sorgu sonucunu al (bir satır veri bekliyoruz)
$card = $stmt_card->fetch(PDO::FETCH_ASSOC);

// *** Kart bulunamazsa ne olacak? ***

// Eğer veritabanında bu ID'ye sahip bir kart yoksa ($card boş dönerse)
if (!$card) {
    // Kullanıcıyı ana sayfaya yönlendir veya bir hata mesajı göster
    header("Location: index.php"); // Şimdilik ana sayfaya yönlendiriyoruz
    exit(); // Yönlendirme sonrası kodun çalışmasını durdur
}

// *** Çekilen kart bilgilerini düzenleme ***

// JSON formatında saklanan sosyal medya bilgilerini al
$social_media_raw = $card['social_media'];

// Sosyal medya JSON metnini PHP dizisine çevir
// Eğer çevirme başarısız olursa veya veri boşsa, boş bir dizi olarak ayarla
$social_media = json_decode($social_media_raw, true);
if (!is_array($social_media)) {
    $social_media = [];
}

// Projeler ve yetenekler bilgilerini veritabanından çekilen kart verisinden al
$projects_text = $card['projects'];
$skills_text = $card['skills'];

// *** Avatar resminin URL'sini belirleme ***

// Kartın avatar numarası doluysa
$avatar_url = ''; // Varsayılan olarak boş bırak
if (!empty($card['avatar'])) {
    $avatar_numarasi = $card['avatar']; // Avatar numarasını al
    
    // Avatar numarasına göre hangi yerel resim dosyasının kullanılacağını belirle (kartolustur.php ile aynı mantık)
    switch ($avatar_numarasi) {
        case 1:
            $avatar_url = "images/normalbeyaz.png";
            break;
        case 2:
            $avatar_url = "images/normalsiyah.png";
            break;
        case 3:
            $avatar_url = "images/normalkadinbeyaz.png";
            break;
        case 4:
            $avatar_url = "images/normalkadinbeyaz2.png";
            break;
        case 5:
            $avatar_url = "images/normalkadinbeyaz3.png";
            break;
        default:
            // Eğer numara 1-5 arasında değilse, varsayılan bir avatar göster (isteğe bağlı)
            $avatar_url = "images/varsayilan_avatar.png"; // Varsayılan avatar dosya yolu
            break;
    }
}

// *** Kartı görüntüleyen kişinin kartın sahibi olup olmadığını kontrol et ***

// Başlangıçta kart sahibi olmadığını varsay
$is_creator = false;

// Eğer kullanıcı oturum açmışsa (username session'da kayıtlıysa)
// VE veritabanından çekilen kartın sahibi bilgisi doluysa
// VE oturumdaki kullanıcı adı, kartın sahibinin kullanıcı adı ile eşleşiyorsa
if (isset($_SESSION['username']) && isset($card['creator_username']) && $card['creator_username'] === $_SESSION['username']) {
    // O zaman bu kullanıcı kartın sahibidir
    $is_creator = true;
}

// Artık $card değişkeni çekilen tüm kart bilgilerini (name, bio, email, phone, social_media, projects, skills, avatar, creator_username) içeriyor.
// $social_media değişkeni sosyal medya linklerini bir dizi olarak içeriyor.
// $projects_text ve $skills_text projeler ve yetenekler metinlerini içeriyor.
// $avatar_url gösterilecek avatar resminin yolunu içeriyor.
// $is_creator ise kullanıcının kart sahibi olup olmadığını (true/false) belirtiyor.
// Bu değişkenler HTML içinde kart bilgilerini göstermek için kullanılacak.

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($card['name']); ?> - Dijital Kart</title>
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
            background-color: #1a1a1a;
            color: white;
        }

        body.dark-mode .card {
            background: #2d2d2d;
            box-shadow: 0 0 10px rgba(255,255,255,0.1);
        }

        body.dark-mode .name {
            color: white;
        }

        body.dark-mode .bio {
            color: #ccc;
        }

        body.dark-mode .section h2 {
            color: white;
            border-bottom-color: #444;
        }

        body.dark-mode .projects li,
        body.dark-mode .skills li {
            background: #3d3d3d;
        }

        body.dark-mode .creator-message {
            background-color: #2d3d4d;
            color: #fff;
        }

        body.dark-mode .contact-info a,
        body.dark-mode .social-links a {
            color: #66b3ff;
        }

        body.dark-mode .section {
            background-color: #3d3d3d; /* Gece modu için bölümlere daha koyu arka plan rengi */
        }

        body.dark-mode .contact-info a,
        body.dark-mode .social-links a {
            color: #66b3ff;
        }

        body.dark-mode .copy-message {
            color: #bbb;
        }

        .card {
            max-width: 400px;
            margin: 15px auto;
            padding: 15px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 3px solid #ddd;
        }

        .name {
            font-size: 2em;
            color: #333;
            margin-bottom: 10px;
        }

        .bio {
            color: #666;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 15px;
            background-color: #f9f9f9;
            padding: 12px;
            border-radius: 8px;
        }

        .section h2 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .contact-info a {
            color: #007bff;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .social-links a {
            display: inline-block;
            margin-right: 8px;
            color: #007bff;
            text-decoration: none;
        }

        .social-links a:hover {
            text-decoration: underline;
        }

        .projects, .skills {
            list-style: none;
        }

        .projects li, .skills li {
            margin-bottom: 5px;
            padding: 5px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .section p {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .creator-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
        }

        .copy-button {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        .copy-button:hover {
            background-color: #0056b3;
        }

        .copy-message {
            margin-top: 10px;
            font-size: 0.9em;
            color: #555;
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
    
    <div class="card"> <!-- Kartın ana kutusu -->

<!-- Kart sahibi mesaj alanı -->
<div class="creator-message"> <!-- Mesaj kutusu stilleri -->
    <p>>Bu kartın linki:</p> <!-- Açıklama metni -->
    <input type="text" id="cardLink" value="http://localhost/dijital%20kart/kart.php?id=<?php echo $id; ?>" readonly style="width: 80%; padding: 5px; border-radius: 5px; border: 1px solid #ccc;">
    <button onclick="copyCardLink()" class="copy-button">Kopyala</button>
    <p id="copyConfirmation" class="copy-message" style="display:none;"></p>
</div>

<div class="header"> <!-- Başlık ve avatar bölümü -->
    <!-- Avatar resmi alanı -->
    <?php if (!empty($avatar_url)): // Avatar URL'si doluysa göster ?>
        <img src="<?php echo $avatar_url; ?>" alt="Profil Fotoğrafı" class="avatar"> <!-- Avatar resmi -->
    <?php endif; // Avatar alanı sonu ?>
    <h1 class="name"><?php echo htmlspecialchars($card['name']); ?></h1> <!-- Ad Soyad -->
    <p class="bio"><?php echo nl2br(htmlspecialchars($card['bio'])); ?></p> <!-- Biyografi metni -->
</div>

<!-- İletişim bilgileri bölümü -->
<?php if (!empty($card['email']) || !empty($card['phone'])): // Email veya telefon bilgisi varsa göster ?>
<div class="section"> <!-- Bölüm kutusu -->
    <h2>İletişim</h2> <!-- Bölüm başlığı -->
    <div class="contact-info"> <!-- İletişim bilgileri listesi -->
        <?php if (!empty($card['email'])): // Email doluysa göster ?>
            <span><?php echo htmlspecialchars($card['email']); ?></span> <!-- Email adresi -->
        <?php endif; // Email sonu ?>

        <?php if (!empty($card['phone'])): // Telefon doluysa göster ?>
            <span><?php echo htmlspecialchars($card['phone']); ?></span> <!-- Telefon numarası -->
        <?php endif; // Telefon sonu ?>
    </div>
</div>
<?php endif; // İletişim bölümü sonu ?>

<!-- Sosyal medya bölümü -->
<?php if (!empty($social_media)): // Sosyal medya bilgisi varsa göster ?>
<div class="section"> <!-- Bölüm kutusu -->
    <h2>Sosyal Medya</h2> <!-- Bölüm başlığı -->
    <div class="social-links"> <!-- Sosyal medya linkleri listesi -->
        <?php if (!empty($social_media['instagram'])): // Instagram doluysa göster ?>
            <a href="https://www.instagram.com/<?php echo htmlspecialchars($social_media['instagram']); ?>" target="_blank">Instagram</a> <!-- Instagram linki -->
        <?php endif; // Instagram sonu ?>

        <?php if (!empty($social_media['twitter'])): // Twitter doluysa göster ?>
            <a href="https://x.com/<?php echo htmlspecialchars($social_media['twitter']); ?>" target="_blank">Twitter</a> <!-- Twitter linki -->
        <?php endif; // Twitter sonu ?>

        <?php if (!empty($social_media['linkedin'])): // LinkedIn doluysa göster ?>
            <a href="<?php echo htmlspecialchars($social_media['linkedin']); ?>" target="_blank">LinkedIn</a> <!-- LinkedIn linki -->
        <?php endif; // LinkedIn sonu ?>

        <?php if (!empty($social_media['github'])): // GitHub doluysa göster ?>
            <a href="https://github.com/<?php echo htmlspecialchars($social_media['github']); ?>" target="_blank">GitHub</a> <!-- GitHub linki -->
        <?php endif; // GitHub sonu ?>
    </div>
</div>
<?php endif; // Sosyal medya bölümü sonu ?>

<!-- Projeler bölümü -->
<div class="section"> <!-- Bölüm kutusu -->
    <h2>Projeler</h2> <!-- Bölüm başlığı -->
    <p><?php echo nl2br(htmlspecialchars($projects_text)); ?></p> <!-- Projeler metni -->
</div>

<!-- Yetkinlikler bölümü -->
<div class="section"> <!-- Bölüm kutusu -->
    <h2>Yetkinlikler</h2> <!-- Bölüm başlığı -->
    <p><?php echo nl2br(htmlspecialchars($skills_text)); ?></p> <!-- Yetkinlikler metni -->
</div>
</div> <!-- Kart ana kutusu sonu -->

    <script>
        // Tema değiştirme fonksiyonu
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
        }

        function copyCardLink() {
            const cardLinkInput = document.getElementById('cardLink');
            cardLinkInput.select();
            cardLinkInput.setSelectionRange(0, 99999); // For mobile devices

            navigator.clipboard.writeText(cardLinkInput.value).then(() => {
                const copyConfirmation = document.getElementById('copyConfirmation');
                copyConfirmation.innerText = 'Link kopyalandı!';
                copyConfirmation.style.display = 'block';
                setTimeout(() => {
                    copyConfirmation.style.display = 'none';
                }, 2000);
            }).catch(err => {
                console.error('Link kopyalanamadı:', err);
                alert('Link kopyalanamadı. Lütfen manuel olarak kopyalayın.');
            });
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
