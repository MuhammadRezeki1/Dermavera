# Spesifikasi Animasi dan Interaksi Anime.js

## 1. Kedudukan Anime.js

Anime.js adalah lapisan presentasi untuk memperjelas perubahan state pada antarmuka. Library ini tidak menentukan kelayakan produk, skor C1-C6, bobot, normalisasi, safety gate, atau ranking. Seluruh nilai keputusan berasal dari Laravel dan snapshot database.

Dokumen ini menjadi kontrak implementasi agar animasi konsisten, aksesibel, dapat diuji, dan tidak berlebihan.

## 2. Modul Anime.js yang digunakan

| Modul | Penggunaan |
|---|---|
| `animate` | Opacity, transform, width, warna, dan nilai objek |
| `createTimeline` | Urutan hero, wizard, processing, dan hasil |
| `stagger` | Daftar kartu, ingredients, dan ranking |
| `createScope` | Isolasi animasi per komponen Livewire dan cleanup |
| `onScroll` | Reveal section edukasi dan metode |
| `splitText` | Judul hero per kata; tidak untuk teks medis penting |
| `createLayout` | Perubahan grid katalog dan modal atau detail |
| `svg` | Ikon centang, garis proses, dan ilustrasi loading singkat |
| `createDraggable` | Carousel atau perbandingan mobile opsional |

Gunakan import modular agar hanya kode yang diperlukan masuk bundle.

## 3. Instalasi

```bash
npm install animejs
```

Contoh import:

```javascript
import {
  animate,
  createTimeline,
  stagger,
  createScope,
  onScroll,
  splitText,
  createLayout,
} from 'animejs';
```

## 4. Struktur file

```text
resources/js/
  app.js
  animations/
    boot.js
    tokens.js
    motion-preferences.js
    home.js
    consultation.js
    processing.js
    results.js
    catalog.js
    feedback.js
```

Setiap modul mengekspor fungsi `mount(root)` yang mengembalikan fungsi cleanup.

## 5. Motion tokens

```javascript
export const motion = {
  fast: 180,
  base: 320,
  page: 520,
  score: 900,
  stagger: 70,
  distanceSmall: 12,
  distancePage: 28,
};
```

Nilai boleh disesuaikan setelah UAT, tetapi harus konsisten pada seluruh halaman.

## 6. Matriks interaksi

| ID | Halaman atau komponen | Pemicu | Target | Efek | Durasi | Reduced motion |
|---|---|---|---|---|---:|---|
| AN-01 | Navbar | Route berubah | Indikator aktif | Geser horizontal | 240 ms | Langsung pindah |
| AN-02 | Mobile menu | Klik tombol | Panel dan backdrop | Slide + fade | 280 ms | Fade singkat |
| AN-03 | Hero | Halaman masuk | Judul | Reveal per kata | 520 ms + stagger | Tampil langsung |
| AN-04 | Hero | Halaman masuk | Gambar produk | Fade + translate | 600 ms | Fade 100 ms |
| AN-05 | Concern cards | Masuk viewport | Kartu | Fade + stagger | 420 ms | Tanpa stagger |
| AN-06 | Choice card | Pilihan berubah | Kartu dan centang | Scale + border | 180 ms | Border langsung |
| AN-07 | Wizard | Lanjut atau kembali | Panel pertanyaan | Slide + fade | 360 ms | Fade 100 ms |
| AN-08 | Stepper | Tahap berubah | Progress | Width menuju nilai server | 320 ms | Langsung |
| AN-09 | Validation | Respons invalid | Pertanyaan | Shake 4-6 px | 220 ms | Tanpa shake; fokus error |
| AN-10 | Processing | Stage server berubah | Daftar status | Check dan highlight | 200 ms | Langsung |
| AN-11 | Result cards | Hasil tersedia | Top 3 | Fade-up berurutan | 480 ms | Tampil langsung |
| AN-12 | Score counter | Hasil tersedia | Angka skor | 0 ke skor final | 900 ms | Nilai final langsung |
| AN-13 | Criterion bar | Hasil tersedia | Bar C1-C6 | Width ke kontribusi | 800 ms | Width final langsung |
| AN-14 | Catalog | Filter berubah | Grid produk | Auto Layout | 350 ms | Render akhir langsung |
| AN-15 | Source modal | Buka atau tutup | Dialog | Scale + fade | 240 ms | Fade singkat |
| AN-16 | Toast | Aksi selesai | Toast | Slide + fade | 220 ms | Fade singkat |

## 7. Beranda

Urutan timeline hero:

1. Navbar dan logo tampil.
2. Judul hero muncul per kata.
3. Paragraf penjelasan muncul.
4. CTA tampil.
5. Ilustrasi atau gambar produk muncul.
6. Kartu masalah kulit tampil dengan stagger.

Judul hanya dipecah untuk tampilan. Teks asli harus tetap dapat dipulihkan saat scope direvert.

## 8. Wizard konsultasi

Transisi dijalankan setelah validasi Livewire berhasil. Urutan yang disarankan:

```text
disable navigation briefly
animate current panel out
render next state from Livewire
animate next panel in
move focus to heading
enable navigation
```

Durasi penguncian tombol maksimal mengikuti transisi 360 ms. Jangan menunggu animasi jika respons memuat red flag.

## 9. Validasi dan red flag

- Error biasa boleh memakai shake ringan, border merah, dan fokus ke pesan.
- Red flag tidak menggunakan animasi dekoratif, confetti, bounce, atau delay.
- Banner red flag tersedia segera dan mendapat fokus sesuai pola aksesibilitas.
- Timeline lain dihentikan atau direvert ketika safety outcome adalah `refer`.

## 10. Processing state

Anime.js boleh menggambar garis SVG atau menggerakkan indikator kecil, tetapi status harus berasal dari event proses yang nyata. Dilarang menampilkan progress persentase palsu jika backend tidak menyediakannya.

Jika hasil selesai sebelum timeline, langsung selesaikan indikator dan tampilkan hasil. Jika proses lambat, gunakan loop ringan pada satu indikator saja.

## 11. Hasil rekomendasi

Angka yang dianimasikan menggunakan objek JavaScript dengan nilai akhir dari server:

```javascript
function animateScore(element, finalScore, reduceMotion) {
  if (reduceMotion) {
    element.textContent = Number(finalScore).toFixed(4);
    return;
  }

  const state = { value: 0 };
  animate(state, {
    value: Number(finalScore),
    duration: 900,
    ease: 'out(3)',
    onRender: () => {
      element.textContent = Number(state.value).toFixed(4);
    },
  });
}
```

Setelah selesai, lakukan assertion pada pengujian bahwa teks sama dengan nilai server. Peringkat pertama boleh mendapat ring atau glow sekali, tetapi tidak boleh terus berdenyut.

## 12. Katalog

Gunakan `createLayout()` untuk perubahan grid setelah filter Livewire selesai. Elemen yang tidak cocok dihapus secara semantik, bukan hanya dibuat transparan. Posisi fokus harus dipertahankan bila elemen pemicu masih ada.

Draggable tidak digunakan untuk mengubah urutan hasil. Pemakaian yang diperbolehkan hanya carousel produk atau panel perbandingan pada layar kecil.

## 13. Integrasi Livewire

Pola dasar:

```javascript
import { createScope } from 'animejs';

const mountedScopes = new WeakMap();

export function mountMotion(root, setup) {
  mountedScopes.get(root)?.revert();

  const scope = createScope({
    root,
    mediaQueries: {
      reduceMotion: '(prefers-reduced-motion: reduce)',
      mobile: '(max-width: 767px)',
    },
  }).add(self => setup(self));

  mountedScopes.set(root, scope);
  return () => {
    scope.revert();
    mountedScopes.delete(root);
  };
}
```

Aturan lifecycle:

- satu scope untuk satu root komponen;
- revert scope lama sebelum mount ulang;
- listener global didaftarkan sekali;
- elemen dinamis dicari setelah render Livewire selesai;
- jangan menyimpan referensi DOM yang sudah diganti;
- jangan memakai selector global untuk daftar hasil atau pertanyaan.

## 14. Reduced motion

Jika `prefers-reduced-motion: reduce` aktif:

- nonaktifkan split text bergerak, stagger, parallax, shake, dan count-up panjang;
- gunakan fade maksimal 100 ms bila perlu untuk konteks;
- langsung tetapkan progress dan skor akhir;
- jangan menghilangkan informasi atau perubahan status;
- jangan mengubah urutan fokus.

## 15. Kinerja

- Utamakan `transform` dan `opacity`.
- Hindari animasi width atau height pada banyak elemen sekaligus; criterion bar yang sedikit masih diperbolehkan.
- Jangan menjalankan scroll animation pada seluruh kartu katalog sekaligus.
- Gunakan import modular dan tree shaking Vite.
- Hentikan observer atau timeline ketika elemen tidak lagi berada di halaman.
- Jangan menjalankan loop pada halaman hasil setelah informasi tampil.

## 16. Pengujian penerimaan

| ID | Skenario | Hasil yang diharapkan |
|---|---|---|
| AT-AN-01 | JavaScript gagal dimuat | Konsultasi dan hasil tetap dapat digunakan |
| AT-AN-02 | Reduced motion aktif | Tidak ada gerakan besar, stagger, atau count-up panjang |
| AT-AN-03 | Navigasi wizard berulang | Tidak ada listener atau animasi ganda |
| AT-AN-04 | Red flag diterima | Banner langsung terlihat dan ranking tidak tampil |
| AT-AN-05 | Hasil SAW diterima | Skor akhir sama persis dengan payload server |
| AT-AN-06 | Filter katalog cepat | Grid stabil dan fokus tidak hilang |
| AT-AN-07 | Resize halaman | Split text atau layout tidak menggandakan node |
| AT-AN-08 | Keyboard only | Semua aksi dapat dilakukan tanpa mouse |

## 17. Yang tidak boleh dibuat

- Loading palsu dengan durasi minimum panjang.
- Confetti untuk hasil medis atau kosmetik.
- Ranking yang dapat diseret pengguna.
- Parallax berlebihan pada form.
- Animasi error tanpa teks.
- Skor acak atau skor sementara yang tidak berasal dari server.
- Timeline yang mencegah pengguna membaca peringatan.

## 18. Sumber resmi

- [Anime.js Documentation](https://animejs.com/documentation/)
- [Module imports](https://animejs.com/documentation/getting-started/module-imports/)
- [Timeline](https://animejs.com/documentation/timeline/)
- [Stagger](https://animejs.com/documentation/utilities/stagger/)
- [Scope](https://animejs.com/documentation/scope/)
- [Scroll Observer](https://animejs.com/documentation/events/onscroll/)
- [SVG](https://animejs.com/documentation/svg/)
- [Text](https://animejs.com/documentation/text/)
- [Layout](https://animejs.com/documentation/layout/)
- [Draggable](https://animejs.com/documentation/draggable/)
