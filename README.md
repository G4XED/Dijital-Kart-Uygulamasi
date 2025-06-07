Uygulamayı çalıştırmak için aşağıdaki adımları takip edin:

1 - Proje Dosyalarını Alın: Proje dosyalarını indirin veya klonlayın.

2 - Yerel Sunucuyu Başlatın: Bilgisayarınızda kurulu olan XAMPP, WAMP, Laragon gibi bir PHP ve MySQL sunucu paketini çalıştırın (Apache ve MySQL servislerinin aktif olduğundan emin olun).

3 - Veritabanını Oluşturun: Tarayıcınızdan localhost/phpmyadmin adresine gidin. dijitalkart adında yeni bir veritabanı oluşturun. Oluşturduğunuz veritabanını seçip İçe Aktar sekmesinde dosyaların arasındaki dijitalkart.sql'i içe aktarın:

4 - Dosyaları Kopyalayın: İndirdiğiniz proje dosyalarını sunucunuzun belge kök dizinine (htdocs veya www klasörü) veya bu klasörlerin içine oluşturacağınız bir proje klasörüne (dijitalkart gibi) kopyalayın.

5 - Veritabanı Bağlantısını Ayarlayın: kartolustur.php, kart.php ve kartduzenle.php dosyalarını açarak başlarındaki veritabanı bağlantı satırını ($db = new PDO(...)) kendi MySQL kullanıcı adı ve şifrenize göre güncelleyin.

6 - Uygulamayı Açın: Web tarayıcınızı açın ve projenizi kopyaladığınız konuma göre http://localhost/dijitalkart/index.php adresine gidin.

Uygulama artık kullanıma hazır olmalıdır.
