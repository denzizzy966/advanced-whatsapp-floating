# Advanced WhatsApp Floating Button Plugin

Plugin WordPress yang canggih untuk menambahkan floating button WhatsApp dengan form kontak lengkap, dashboard analytics, dan pengaturan yang sangat dapat disesuaikan.

## ✨ Fitur Utama

### 🎯 Floating Button WhatsApp
- **Posisi Fleksibel**: 6 pilihan posisi (bottom-right, bottom-left, top-right, top-left, center-right, center-left)
- **Kustomisasi Warna**: Background color dan text color dapat disesuaikan
- **Ukuran Button**: Dapat disesuaikan dari 40px hingga 120px
- **Custom Icon**: Upload icon custom atau gunakan default WhatsApp icon
- **Animasi**: Pulse animation dengan opsi enable/disable
- **Responsive**: Otomatis menyesuaikan dengan berbagai ukuran layar

### 📝 Form Kontak Lengkap
- **5 Field Input**:
  1. **Name** (Nama)
  2. **Email** 
  3. **Phone** (Nomor Telepon)
  4. **Company** (Perusahaan) - dapat disembunyikan
  5. **Message** (Pesan)
- **Validasi Real-time**: Validasi input secara langsung
- **Required Fields**: Dapat mengatur field mana saja yang wajib diisi
- **3 Ukuran Form**: Small, Medium, Large
- **Auto-focus**: Otomatis focus ke field pertama saat form dibuka

### 🎨 Kustomisasi Tampilan
- **Typography**: 7 pilihan font family dan ukuran font dapat disesuaikan
- **Form Styling**: Warna, ukuran, dan style dapat disesuaikan
- **Custom CSS**: Tambahkan CSS kustom untuk styling lanjutan
- **Live Preview**: Preview real-time di halaman settings
- **Dark Mode**: Dukungan untuk dark mode

### 📊 Dashboard & Analytics
- **Dashboard Statistics**:
  - Total contacts
  - Today's contacts  
  - This month's contacts
  - Setup status
- **Recent Contacts**: Daftar 5 kontak terbaru
- **System Status**: Status konfigurasi plugin

### 📋 Manajemen Kontak
- **Contact List**: Daftar lengkap semua kontak dengan paginasi
- **Advanced Filtering**:
  - Search by name, email, company, atau message
  - Filter by status (New, Contacted, Closed)
  - Filter by date range
- **Bulk Actions**:
  - Delete multiple contacts
  - Update status multiple contacts
  - Export selected contacts
- **Contact Details**: View detail lengkap setiap kontak dalam modal
- **Status Management**: Update status kontak (New/Contacted/Closed)

### 📤 Export & Import
- **Export Contacts**: Export semua atau selected contacts ke CSV
- **Export Settings**: Backup pengaturan plugin ke JSON
- **Import Settings**: Restore pengaturan dari file JSON

### ⚙️ Pengaturan Lanjutan
- **Action After Submit**: 3 pilihan (Close Form, Keep Open, Reset Form)
- **Analytics**: Enable/disable penyimpanan data kontak
- **Custom CSS**: Tambahkan styling kustom
- **Required Fields**: Pilih field mana yang wajib diisi

## 🚀 Instalasi

### Method 1: Upload Plugin
1. Download file plugin dalam format ZIP
2. Login ke WordPress Admin
3. Pergi ke **Plugins > Add New > Upload Plugin**
4. Upload file ZIP dan klik **Install Now**
5. Aktivasi plugin

### Method 2: Manual Installation
1. Extract file ZIP plugin
2. Upload folder plugin ke `/wp-content/plugins/`
3. Aktivasi plugin melalui WordPress Admin

## 🔧 Konfigurasi

### 1. Pengaturan Dasar
1. Pergi ke **WhatsApp Floating > Settings**
2. Masukkan **Nomor WhatsApp** (format: 628123456789)
3. Atur **Form Title** dan **Submit Button Text**
4. Pilih **Action After Submit**

### 2. Pengaturan Tampilan
1. Pilih tab **Appearance**
2. Atur **Button Position** dan **Button Size**
3. Sesuaikan **Background Color** dan **Text Color**
4. Upload **Custom Icon** jika diperlukan
5. Enable/disable **Animation**

### 3. Pengaturan Form
1. Pilih tab **Form Settings**
2. Pilih **Form Size** (Small/Medium/Large)
3. Enable/disable **Company Field**
4. Pilih **Required Fields**

### 4. Pengaturan Lanjutan
1. Pilih tab **Advanced**
2. Enable/disable **Analytics**
3. Tambahkan **Custom CSS** jika diperlukan
4. Export/Import pengaturan

## 📱 Cara Penggunaan

### Untuk Pengunjung Website
1. Klik floating button WhatsApp di website
2. Isi form kontak yang muncul
3. Klik tombol "Send to WhatsApp"
4. Akan otomatis terbuka WhatsApp dengan pesan terformat

### Untuk Admin
1. **Dashboard**: Lihat statistik dan kontak terbaru
2. **Contacts**: Kelola semua kontak, filter, dan export
3. **Settings**: Kustomisasi tampilan dan fungsi plugin

## 📋 Persyaratan Sistem

- **WordPress**: 5.0 atau lebih baru
- **PHP**: 7.4 atau lebih baru
- **MySQL**: 5.6 atau lebih baru
- **Browser**: Modern browsers (Chrome, Firefox, Safari, Edge)

## 🎯 Kompatibilitas

### WordPress Versions
- ✅ WordPress 6.8.2 (Tested)
- ✅ WordPress 6.7.x
- ✅ WordPress 6.6.x
- ✅ WordPress 6.5.x

### Themes
- ✅ Semua theme yang mengikuti standar WordPress
- ✅ Page builders (Elementor, Divi, Beaver Builder)
- ✅ WooCommerce themes

### Plugins
- ✅ WooCommerce
- ✅ Contact Form 7
- ✅ Yoast SEO
- ✅ Caching plugins (WP Rocket, W3 Total Cache)

## 🔧 Kustomisasi CSS

### Selector CSS Utama
```css
/* Container utama */
.awf-floating-container { }

/* Floating button */
.awf-floating-button { }

/* Contact form */
.awf-contact-form { }

/* Form inputs */
.awf-form-input { }

/* Submit button */
.awf-submit-button { }
```

### Contoh Kustomisasi
```css
/* Ubah shadow button */
.awf-floating-button {
    box-shadow: 0 8px 32px rgba(37, 211, 102, 0.3);
}

/* Custom form styling */
.awf-contact-form {
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

/* Custom input styling */
.awf-form-input:focus {
    border-color: #your-color;
    box-shadow: 0 0 0 3px rgba(your-color, 0.1);
}
```

## 🌍 Format Nomor WhatsApp

Gunakan format internasional tanpa tanda + atau spasi:

- ✅ **Indonesia**: 628123456789
- ✅ **Malaysia**: 60123456789  
- ✅ **Singapore**: 6512345678
- ✅ **USA**: 11234567890
- ❌ **Salah**: +62 812-3456-789

## 🔒 Keamanan & Privacy

- **Data Sanitization**: Semua input data di-sanitize
- **Nonce Verification**: Proteksi CSRF attack
- **Capability Checks**: Hanya admin yang dapat mengakses pengaturan
- **SQL Injection Prevention**: Menggunakan prepared statements
- **XSS Protection**: Output data di-escape

## 🚀 Performance

- **Lazy Loading**: CSS dan JS dimuat hanya saat diperlukan
- **Minified Assets**: File CSS dan JS ter-optimasi
- **Database Optimization**: Query ter-optimasi untuk performa
- **Caching Friendly**: Kompatibel dengan plugin caching
- **Mobile Optimized**: Performa optimal di mobile

## 🐛 Troubleshooting

### Button Tidak Muncul
1. Pastikan nomor WhatsApp sudah diisi
2. Cek apakah ada konflik CSS dengan theme
3. Disable plugin caching sementara

### Form Tidak Submit
1. Pastikan field required sudah diisi
2. Cek koneksi internet
3. Pastikan nomor WhatsApp valid

### Export Tidak Bekerja
1. Pastikan ada data kontak
2. Cek permission file di server
3. Disable popup blocker di browser

## 📞 Support

Jika Anda mengalami masalah atau membutuhkan bantuan:

1. **Documentation**: Baca dokumentasi lengkap ini
2. **WordPress Forum**: Post di forum WordPress.org
3. **GitHub Issues**: Laporkan bug di repository GitHub
4. **Email Support**: Hubungi developer via email

## 🔄 Changelog

### Version 2.0.0 (Current)
- ✨ Complete rewrite dengan fitur advanced
- ✨ Dashboard analytics dan contact management
- ✨ Advanced filtering dan bulk actions
- ✨ Export/Import functionality
- ✨ Live preview di settings
- ✨ Mobile-first responsive design
- ✨ Dark mode support
- ✨ Custom CSS editor
- ✨ Better security dan performance

### Version 1.0.0
- 🎉 Initial release
- Basic floating button
- Simple contact form
- Basic settings panel

## 📄 License

Plugin ini dilisensikan di bawah GPL v2 atau yang lebih baru. Anda bebas untuk menggunakan, memodifikasi, dan mendistribusikan plugin ini sesuai dengan ketentuan lisensi GPL.

## 🙏 Credits

- **WhatsApp Icon**: Official WhatsApp brand guidelines
- **UI Framework**: Menggunakan WordPress admin UI standards
- **Icons**: Dashicons dan custom SVG icons
- **Animations**: CSS3 transitions dan keyframes

---

**Dibuat dengan ❤️ untuk komunitas WordPress Indonesia**

*Plugin ini dibuat khusus untuk memenuhi kebutuhan website Indonesia dengan dukungan WhatsApp yang optimal dan fitur-fitur yang sesuai dengan kebutuhan lokal.*