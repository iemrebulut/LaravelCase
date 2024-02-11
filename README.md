## <p align="center">Laravel Api Case</p>
<p align="center">Emre BULUT</p>

<p align="center"> 
    <img src="https://skillicons.dev/icons?i=php,laravel,mysql" />
</p>

Backend repository: https://github.com/iemrebulut/LaravelCase.git

## Kurulum Aşamaları

- Clone repository
- "composer install" komutunu çalıştırın
- .env.example dosyasını yeniden adlandırın veya .env olarak kopyalayın
- “php artisan key:generate” komutunu çalıştırın.
- .env dosyasında DB_DATABASE=laravel_case olarak düzenleyin
- MySQL serverını başlatın
- laravel_case adında bir veritabanı oluşturun
- "php artisan migrate" komutunu çalıştırın
- "php artisan db:seed" komutunu çalıştırın
- "php artisan queue:work" komutunu çalıştırın
- "php artisan serve" komutunu çalıştırın
- Postman uygulamasını açın ve install klasörü alında bulunan "LaravelApiCase - IDEASOFT.postman_collection.json" dosyasını import edin. (Port numaranız farklı ise url kısımlarını düzenleyin)
- Login requestini çalıştırın ve dönen result içerisindeki token datasını kopyalayın
- Create Order requestine giderek Authorization kısmından Type'ı Bearer Token olarak değiştirin. Gerekli alana kopyaladığınız token datasını yapıştırın. Create Order requestini çalıştırabilirsiniz.
- Request sonucu dönen sip_no'yu, Order Details requestinde url'nin sonuna yapıştırın. Aynı şekilde authorization kısmınden Type'ı Bearer Token olarak değiştirip gerekli alana token bilgisini yapıştırın. Order Details requestini çalıştırın. Sipariş bilgilerine ulaşabilirsiniz.

## ENDPOINTS

- (POST) login: Login işlemi için gerekli endpoint.
    - Body: {"email":"admin@admin.com", "password":"123456"}
- (POST) logout: Logout işlemi için gerekli endpoint. Authorization: Bearer Token gereklidir.
- (POST) order: Sipariş oluşturmak için gerekli endpoint. Birden fazla ürün ile işlem yapılabilir. "quantity" ile üründen kaç adet sipariş verildiği bildirilir. Authorization: Bearer Token gereklidir.
    - Body: [{"productId":3, "quantity":1},{"productId":5,"quantity":1}]
- (GET) order/list/{orderId}: Sipariş bilgilerini döndüren endpoint. Siparişteki ürünler, kampanyalar, sipariş tutarı bilgilerine ulaşılabilir. Bearer Token gereklidir.
- (POST) order/delete: Siparişi silen endpoint. Sipariş id bilgisi ile işlem yapılır. Authorization: Bearer Token gereklidir.
    - Body: [{"orderId":1}]
- (GET) order/discounts/{orderId}: Sipariş içerisindeki kampanya bilgilerini döndüren endpoint. Sipariş id bilgisi ile işlem yapılır. Authorization: Bearer Token gereklidir.
