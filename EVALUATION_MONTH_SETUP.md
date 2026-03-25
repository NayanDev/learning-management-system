# Instruksi Setup Evaluasi Month

## 1. Jalankan Migration
Jalankan perintah berikut di terminal untuk menambahkan kolom 'category' ke tabel evaluation_months:

```bash
php artisan migrate
```

## 2. Cara Menggunakan

### Input Data Evaluasi
- Akses: `/evaluation-month?token={EVENT_TOKEN}`
- Isi nilai 0-20 untuk setiap aspek penilaian
- Sistem otomatis menghitung kategori:
  - **A (Sangat Baik)**: 16-20
  - **B (Baik)**: 11-15
  - **C (Cukup)**: 6-10
  - **D (Kurang)**: 0-5

### Lihat Hasil Evaluasi
- Akses: `/evaluation-month-result?token={EVENT_TOKEN}`
- Atau klik tombol "Lihat Hasil" di halaman form
- Data akan tampil dengan kategori nilai (A, B, C, D)
- Kolom P.I akan menampilkan kategori yang sama

## 3. Field Penilaian

### Aspek Teknis Pekerjaan
1. Efektivitas & Efisiensi Kerja
2. Ketepatan Waktu Dalam Menyelesaikan Tugas
3. Kemampuan Mencapai Target
4. Tertib Administrasi
5. Inisiatif
6. Kerjasama / Koordinasi Antar Bagian

### Aspek Kepribadian
7. Perilaku
8. Kedisiplinan
9. Tanggung Jawab & Loyalitas
10. Ketaatan Terhadap Instruksi Kerja

### Aspek Kepemimpinan
11. Koordinasi Bawahan
12. Kontrol / Pengendalian Bawahan
13. Evaluasi dan Pembinaan Bawahan
14. Delegasi Tanggung Jawab dan Wewenang
15. Kecepatan & Ketepatan Pengambilan Keputusan

## 4. Fitur
- ✅ Kategori otomatis berdasarkan nilai
- ✅ Validasi input 0-20
- ✅ Multi-participant support
- ✅ Progress tracker
- ✅ Export/Print hasil
- ✅ Redirect otomatis ke hasil setelah save
