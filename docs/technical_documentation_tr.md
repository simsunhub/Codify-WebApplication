# 📚 Eğitim Otomasyon Platformu — Teknik Proje Dokümantasyonu

**Proje Adı:** EduPlatform — Udemy Tarzı Eğitim Otomasyon Sistemi  
**Teknoloji Yığını:** Laravel 11/12, PostgreSQL, Bootstrap 5  
**Proje Türü:** Üniversite Akademik Bitirme Projesi  
**Yazar:** Proje Ekibi  
**Tarih:** Mayıs 2026  

---

## 📋 İçindekiler

1. [Proje Amacı ve Kapsamı](#1-proje-amacı-ve-kapsamı)
2. [Veritabanı Yapısal Mantığı](#2-veritabanı-yapısal-mantığı)
3. [Kurumsal Düzeyde Güvenlik Önlemleri](#3-kurumsal-düzeyde-güvenlik-önlemleri)
4. [Mimari Gerekçelendirme](#4-mimari-gerekçelendirme)
5. [Sistem Aktörleri ve Yetkilendirme](#5-sistem-aktörleri-ve-yetkilendirme)
6. [Sonuç ve Değerlendirme](#6-sonuç-ve-değerlendirme)

---

## 1. Proje Amacı ve Kapsamı

### 1.1 Problem Tanımı

Geleneksel eğitim sistemleri, coğrafi sınırlamalar, sınırlı sınıf kapasitesi ve esnek olmayan ders programları nedeniyle modern öğrencilerin ihtiyaçlarını tam olarak karşılayamamaktadır. Dijital dönüşüm çağında, bilgiye erişimin demokratikleştirilmesi ve her bireyin kendi hızında, kendi zamanında öğrenebileceği bir platform gerekliliği ortaya çıkmıştır.

Bu projede, **Udemy** ve benzeri başarılı çevrimiçi öğrenme platformlarından ilham alınarak, tam teşekküllü bir **Eğitim Otomasyon Sistemi** (Learning Management System — LMS) geliştirilmiştir.

### 1.2 Proje Kapsamı

Platform, aşağıdaki temel işlevleri kapsamaktadır:

| İşlev Alanı | Açıklama |
|---|---|
| **Kurs Yönetimi** | Eğitmenler tarafından kurs oluşturma, düzenleme, silme (CRUD) işlemleri |
| **Ders Modülleri** | Her kursa ait çoklu ders içeriklerinin sıralı şekilde sunumu |
| **Öğrenci Kayıt Sistemi** | Kurslara kayıt olma, ilerleme takibi, sertifika alma |
| **Rol Tabanlı Yetkilendirme** | Öğrenci, Eğitmen ve Yönetici rollerine göre erişim kontrolü |
| **Mesajlaşma ve Bildirim** | Kullanıcılar arası iletişim ve sistem bildirimleri |
| **Arama ve Filtreleme** | Kurs kataloğunda gelişmiş arama ve kategori filtreleme |

### 1.3 Erişim Tanımları — Sistem Aktörleri

Sistemdeki temel aktörler ve yetkileri aşağıdaki tabloda belirtilmiştir:

| Aktör | Rol Kodu | Erişim Hakları |
|---|---|---|
| **Öğrenci** *(Student)* | `student` | Kurs kataloğunu görüntüleme, kurslara kayıt olma, ders içeriklerini izleme, yorum ve değerlendirme yapma, sertifika indirme |
| **Eğitmen** *(Instructor)* | `instructor` | Kendi kurslarını oluşturma/düzenleme/silme, ders modüllerini yönetme, öğrenci istatistiklerini görüntüleme, mesaj gönderme |
| **Yönetici** *(Admin)* | `admin` | Tüm kullanıcıları ve kursları yönetme, kategori yönetimi, duyuru oluşturma, iletişim formlarını inceleme |

---

## 2. Veritabanı Yapısal Mantığı

### 2.1 İlişkisel Bütünlük Şeması (Relational Integrity Schema)

Veritabanı mimarisi, ilişkisel veritabanı tasarım prensipleri *(Normalizasyon — 3NF)* doğrultusunda yapılandırılmıştır.

```
┌──────────────────┐          ┌────────────────────┐          ┌──────────────────┐
│     USERS        │          │     COURSES         │          │     LESSONS      │
├──────────────────┤          ├────────────────────┤          ├──────────────────┤
│ id (PK)          │◄─┐       │ id (PK)            │◄─┐       │ id (PK)          │
│ name             │  │       │ instructor_id (FK) │──┘       │ course_id (FK)   │──┘
│ email (UNIQUE)   │  │       │ category_id (FK)   │          │ title            │
│ password (HASH)  │  │       │ title              │          │ content_text     │
│ role (ENUM)      │  │       │ slug (UNIQUE)      │          │ video_url        │
│ avatar           │  │       │ description        │          │ duration_minutes │
│ bio              │  │       │ price (DECIMAL)    │          │ sort_order       │
│ timestamps       │  │       │ image_path         │          │ timestamps       │
└──────────────────┘  │       │ status (ENUM)      │          └──────────────────┘
                      │       │ level              │
                      │       │ timestamps         │
                      │       └────────────────────┘
                      │
                      │       ┌──────────────────────┐
                      │       │    ENROLLMENTS       │
                      │       ├──────────────────────┤
                      └───────│ user_id (FK)         │
                              │ course_id (FK)       │
                              │ timestamps           │
                              └──────────────────────┘
```

### 2.2 Bire-Çok İlişkiler (One-to-Many Associations)

| İlişki | Açıklama | Kısıtlama |
|---|---|---|
| **Users → Courses** | Bir eğitmen birden fazla kurs oluşturabilir | `instructor_id` → `users.id` (ON DELETE CASCADE) |
| **Courses → Lessons** | Bir kurs birden fazla ders modülüne sahip olabilir | `course_id` → `courses.id` (ON DELETE CASCADE) |
| **Users → Enrollments** | Bir öğrenci birden fazla kursa kayıt olabilir | `user_id` → `users.id` (ON DELETE CASCADE) |
| **Courses → Enrollments** | Bir kursa birden fazla öğrenci kayıt olabilir | `course_id` → `courses.id` (ON DELETE CASCADE) |

### 2.3 Laravel Migration Yapısı

Veritabanı tabloları, Laravel'in migration sistemi kullanılarak oluşturulmuştur. Bu yaklaşımın avantajları:

- **Sürüm Kontrolü:** Veritabanı şeması, Git ile izlenebilir versiyon geçmişine sahiptir
- **Çevre Bağımsızlığı:** Aynı migration dosyaları farklı ortamlarda (geliştirme, test, üretim) çalıştırılabilir
- **Geri Alma Desteği:** Her migration, `up()` ve `down()` metotlarıyla ileri-geri alma yeteneğine sahiptir

```php
// Örnek: Kurslar tablosu migration yapısı
Schema::create('courses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('instructor_id')
          ->constrained('users')
          ->onDelete('cascade');
    $table->foreignId('category_id')
          ->constrained()
          ->onDelete('cascade');
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2)->default(0);
    $table->string('image_path')->nullable();
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->string('level')->default('beginner');
    $table->timestamps();
});
```

### 2.4 ENUM Veri Tipleri

| Tablo | Alan | Değerler | Açıklama |
|---|---|---|---|
| `users` | `role` | `student`, `instructor`, `admin` | Kullanıcı rolü |
| `courses` | `status` | `draft`, `published` | Kurs yayın durumu |

---

## 3. Kurumsal Düzeyde Güvenlik Önlemleri

### 3.1 Bcrypt Şifre Hashleme

Kullanıcı şifreleri, **bcrypt** algoritması ile tek yönlü hash işlemine tabi tutulmaktadır. Bu algoritma:

- **Salt (Tuz) Değeri:** Her hash işlemine otomatik olarak rastgele bir salt eklenir
- **Work Factor:** Varsayılan olarak 12 rounds (2^12 = 4096 iterasyon) kullanılır
- **Tek Yönlü İşlem:** Hash değerinden orijinal şifre geri çözülemez

```php
// Laravel Bcrypt uygulaması
'password' => Hash::make($request->password)
// Sonuç: $2y$12$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 3.2 Laravel Oturum Koruması (Session Protection)

Laravel, oturum güvenliği için çoklu koruma katmanları sunar:

- **Session Regeneration:** Başarılı giriş sonrası oturum kimliği yenilenir (`$request->session()->regenerate()`)
- **Session Invalidation:** Çıkış yapıldığında tüm oturum verisi temizlenir
- **Encrypted Cookies:** Oturum tanımlama bilgileri şifrelenmiş çerezlerde saklanır
- **Session Fixation Koruması:** Saldırganların önceden belirlenmiş oturum kimlikleri ile giriş yapması engellenir

### 3.3 CSRF Token Doğrulama

Tüm POST, PUT, PATCH ve DELETE isteklerinde **Cross-Site Request Forgery** koruması uygulanır:

```blade
<form method="POST" action="/course">
    @csrf  {{-- CSRF token otomatik oluşturulur --}}
    ...
</form>
```

- **Token Yapısı:** Her form için benzersiz, tahmin edilemez token üretilir
- **Middleware:** `VerifyCsrfToken` middleware'i tüm yazma işlemlerini doğrular
- **Otomatik Red:** Geçersiz veya eksik token ile gelen istekler `419` hata kodu ile reddedilir

### 3.4 Katmanlı Girdi Doğrulama (Backend Input Validation)

Kullanıcıdan gelen tüm veriler, sunucu tarafında katı doğrulama kurallarına tabi tutulur:

```php
$request->validate([
    'title'       => ['required', 'string', 'max:255'],
    'category_id' => ['required', 'exists:categories,id'],
    'price'       => ['required', 'numeric', 'min:0', 'max:9999.99'],
    'status'      => ['nullable', Rule::in(['draft', 'published'])],
    'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
]);
```

| Güvenlik Kuralı | Açıklama |
|---|---|
| `Rule::in()` | Sadece izin verilen değerlerin kabul edilmesini sağlar (enjeksiyon koruması) |
| `exists:table,column` | İlişkisel bütünlük — referans verilen kaydın varlığını doğrular |
| `max:2048` | Dosya boyutu sınırlaması — DoS saldırılarını önler |
| `mimes:jpg,png,webp` | Dosya türü kısıtlaması — zararlı dosya yükleme saldırılarını önler |

### 3.5 Rol Tabanlı Erişim Kontrolü (RBAC)

Kayıt sırasında kullanıcının yalnızca `student` veya `instructor` rolünü seçebilmesi sağlanır:

```php
'role' => ['required', Rule::in(['student', 'instructor'])]
```

> ⚠️ **Güvenlik Notu:** `admin` rolü yalnızca veritabanı seviyesinde atanabilir. Kullanıcı arayüzünden `admin` rolü seçimi yapılması backend doğrulama kuralları ile engellenmektedir.

### 3.6 Yetkilendirme Middleware'leri

Sistem, özel middleware katmanları ile rotaları korur:

| Middleware | Koruma Alanı | Açıklama |
|---|---|---|
| `auth` | Kimlik doğrulama | Giriş yapmamış kullanıcıları login sayfasına yönlendirir |
| `admin` | Yönetici paneli | Yalnızca `admin` rolüne sahip kullanıcılara erişim izni verir |
| `teacher` | Eğitmen paneli | Yalnızca `instructor` (veya `admin`) rolüne sahip kullanıcılara erişim izni verir |

---

## 4. Mimari Gerekçelendirme

### 4.1 Neden Laravel? (MVC Mimarisi)

Laravel framework'ü, **Model-View-Controller (MVC)** tasarım kalıbını temel alır:

| Katman | Sorumluluk | Proje Örneği |
|---|---|---|
| **Model** | Veritabanı etkileşimi, iş mantığı | `Course.php`, `User.php`, `Lesson.php` |
| **View** | Kullanıcı arayüzü, sunum | `index.blade.php`, `create.blade.php` |
| **Controller** | İstek işleme, yönlendirme | `CourseController.php`, `DashboardController.php` |

**Akademik Gerekçe:**
- **Separation of Concerns (Kaygıların Ayrılması):** Her katman bağımsız olarak geliştirilebilir ve test edilebilir
- **DRY Prensibi:** Blade template kalıtımı (`@extends`, `@yield`) ile kod tekrarı önlenir
- **Eloquent ORM:** SQL enjeksiyon saldırılarına karşı doğal koruma sağlayan nesne-ilişkisel haritalama
- **Artisan CLI:** Veritabanı migration, seeding ve test işlemlerini otomatize eden komut satırı aracı
- **Ecosystem:** Breeze/Jetstream kimlik doğrulama, Factory/Seeder test veri üretimi

### 4.2 Neden PostgreSQL?

PostgreSQL, açık kaynaklı ve ACID uyumlu bir ilişkisel veritabanı yönetim sistemidir.

| Özellik | MySQL | PostgreSQL | Avantaj |
|---|---|---|---|
| **ACID Uyumu** | Kısmi | Tam | Veri bütünlüğü garantisi |
| **ENUM Tipler** | Evet | Evet (daha esnek) | Tip güvenliği |
| **JSON Desteği** | Temel | Gelişmiş (JSONB) | Esnek veri yapıları |
| **Full-Text Search** | Temel | Gelişmiş (tsvector) | Performanslı arama |
| **Eşzamanlılık** | Tablo kilitleme | MVCC | Yüksek eşzamanlı erişim |
| **Cascade Constraints** | Evet | Evet (daha güvenilir) | İlişkisel bütünlük |

**Akademik Gerekçe:**
- **Referans Bütünlüğü:** `foreignId()->constrained()->onDelete('cascade')` yapısı ile alt kayıtların otomatik silinmesi
- **Enum Tip Güvenliği:** Veritabanı seviyesinde geçersiz değerlerin girişini engeller
- **MVCC (Multi-Version Concurrency Control):** Yüksek trafikli uygulamalarda performans kaybı olmaksızın eşzamanlı erişim

### 4.3 Neden Bootstrap 5?

Bootstrap 5, modern ve duyarlı (responsive) web arayüzleri oluşturmak için endüstri standardı CSS framework'üdür.

| Özellik | Açıklama |
|---|---|
| **Responsive Grid System** | 12 sütunlu ızgara sistemi ile tüm ekran boyutlarına uyum |
| **Bileşen Kütüphanesi** | Modal, Carousel, Dropdown, Navbar gibi hazır UI bileşenleri |
| **jQuery Bağımsızlık** | Bootstrap 5, jQuery gerektirmez (Vanilla JS) |
| **Erişilebilirlik (a11y)** | WCAG 2.1 standartlarına uygun ARIA etiketleri |
| **Özelleştirilebilirlik** | CSS değişkenleri ve Sass ile tam tema özelleştirme |

**Projede Kullanım Örnekleri:**
- **Modal Pop-up:** Kurs silme onay iletişim kutusu
- **Responsive Table:** Eğitmen panelindeki kurs listesi
- **Card Grid:** Ana sayfadaki kurs kataloğu
- **Carousel:** Hero bölümündeki öne çıkan kurslar slaytı

---

## 5. Sistem Aktörleri ve Yetkilendirme

### 5.1 Kullanıcı Akış Diyagramı

```
                    ┌─────────────────┐
                    │   KAYIT FORMU   │
                    │  (register)     │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │  Rol Seçimi:    │
                    │  student /      │
                    │  instructor     │
                    └────────┬────────┘
                             │
              ┌──────────────┼──────────────┐
              │              │              │
     ┌────────▼────────┐   Sunucu        ┌──────────▼──────────┐
     │    ÖĞRENCİ      │   Doğrulama     │     EĞİTMEN         │
     │  Anasayfa (/)    │   (admin RED)   │  Dashboard          │
     │  Kurs Kataloğu   │                 │  /instructor/       │
     │  Ders İzleme     │                 │  dashboard          │
     │  Sertifika       │                 │  Kurs CRUD          │
     └─────────────────┘                  │  Ders Yönetimi      │
                                          └─────────────────────┘
```

### 5.2 Giriş Sonrası Yönlendirme Mantığı

```php
// AuthenticatedSessionController.php
$user = auth()->user();

if ($user->role === 'instructor') {
    return redirect('/instructor/dashboard');
} elseif ($user->role === 'student') {
    return redirect('/');
}
```

---

## 6. Sonuç ve Değerlendirme

### 6.1 Proje Kazanımları

Bu proje kapsamında aşağıdaki akademik ve teknik kazanımlar elde edilmiştir:

1. **MVC Mimarisi Uygulaması:** Laravel framework'ü ile endüstri standardında bir web uygulaması geliştirilmiştir
2. **İlişkisel Veritabanı Tasarımı:** PostgreSQL üzerinde normalleştirilmiş (3NF) şema tasarımı ve referans bütünlüğü uygulanmıştır
3. **Güvenlik En İyi Uygulamaları:** Bcrypt, CSRF, RBAC ve katmanlı girdi doğrulama gibi kurumsal güvenlik önlemleri entegre edilmiştir
4. **Duyarlı Tasarım:** Bootstrap 5 ile masaüstü ve mobil cihazlarda kusursuz çalışan bir kullanıcı arayüzü oluşturulmuştur
5. **Factory/Seeder Pattern:** Otomatik test verisi üretimi ile geliştirme sürecinin hızlandırılması

### 6.2 Gelecek Geliştirmeler

| Öncelik | Geliştirme | Açıklama |
|---|---|---|
| Yüksek | API Geliştirme | RESTful API ile mobil uygulama entegrasyonu |
| Yüksek | Ödeme Sistemi | Stripe/PayPal entegrasyonu ile gerçek ödeme işlemleri |
| Orta | Video Streaming | HLS/DASH ile adaptif video akışı |
| Orta | Gerçek Zamanlı Bildirimler | Laravel WebSockets ile anlık bildirimler |
| Düşük | Çoklu Dil Desteği | Laravel Localization ile i18n altyapısı |

---

> **Not:** Bu dokümantasyon, EduPlatform projesinin teknik yapısını akademik bir perspektifle sunan bir özet niteliğindedir. Detaylı kod incelemesi için projenin kaynak koduna başvurulması önerilir.

---

*Son Güncelleme: Mayıs 2026*  
*Belge Sürümü: 1.0*
