<?php
session_start();
include '../../config/database.php'; 

// 🔒 PROTEKSI: Hanya Admin atau Operator yang boleh mengekstrak data CSV
$role_diizinkan = ['admin', 'operator'];
if (!isset($_SESSION['nama_lengkap']) || !in_array($_SESSION['role'], $role_diizinkan)) {
    die("Akses Ditolak! Anda tidak memiliki otoritas untuk mengunduh data ini.");
}

try {
    // 1. Set Header HTTP agar browser otomatis mengunduh sebagai file Excel CSV
    $filename = "DATA_PENDAFTAR_PPDB_" . date('Y-m-d_H-i-s') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // 2. Membuka jalur output streaming PHP
    $output = fopen('php://output', 'w');

    // 3. Tambahkan BOM (Byte Order Mark) agar Microsoft Excel langsung membaca format UTF-8 dengan rapi (tidak berantakan)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // 4. Definisikan Pemetaan (Mapping) Judul Kolom di Excel vs Nama Kolom di Database kamu
    // Ini memastikan seluruh data Seksi A, B, C, D terisi lengkap tanpa ada yang tertinggal
    $mapping = [
        'no_pendaftaran'        => 'No Registrasi',
        'nisn'                  => 'NISN',
        'nama_siswa'            => 'Nama Lengkap',
        'nama_panggilan'        => 'Nama Panggilan',
        'jenis_kelamin'         => 'L/P',
        'tempat_lahir'          => 'Tempat Lahir',
        'tanggal_lahir'         => 'Tanggal Lahir',
        'agama'                 => 'Agama',
        'anak_ke'               => 'Anak Ke',
        'status_keluarga'       => 'Status Keluarga',
        'jml_saudara_kandung'   => 'Sdr Kandung',
        'jml_adik'              => 'Jumlah Adik',
        'jml_kakak'             => 'Jumlah Kakak',
        'asal_sekolah'          => 'Asal TK/RA',
        'alamat'                => 'Alamat Tinggal',
        'rt'                    => 'RT',
        'rw'                    => 'RW',
        'desa'                  => 'Desa/Kel',
        'kec'                   => 'Kecamatan',
        'kota'                  => 'Kota/Kab',
        
        // Seksi B
        'hobi'                  => 'Hobi',
        'bidang_studi'          => 'Bidang Studi',
        'olahraga'              => 'Olahraga',
        'cita_cita'             => 'Cita-Cita',
        
        // Seksi C (Orang Tua)
        'nama_ayah'             => 'Nama Ayah',
        'tmpt_lahir_ayah'       => 'Tempat Lahir Ayah',
        'tgl_lahir_ayah'        => 'Tgl Lahir Ayah',
        'pekerjaan_ayah'        => 'Pekerjaan Ayah',
        'pendidikan_ayah'       => 'Pendidikan Ayah',
        'status_ayah'           => 'Status Ayah',
        'no_telp_ayah'          => 'No Telp Ayah',
        'nama_ibu'              => 'Nama Ibu',
        'tmpt_lahir_ibu'        => 'Tempat Lahir Ibu',
        'tgl_lahir_ibu'         => 'Tgl Lahir Ibu',
        'pekerjaan_ibu'         => 'Pekerjaan Ibu',
        'pendidikan_ibu'        => 'Pendidikan Ibu',
        'status_ibu'            => 'Status Ibu',
        'no_telp_ibu'           => 'No Telp Ibu',
        'alamat_ortu'           => 'Alamat Orang Tua',
        
        // Seksi D (Wali)
        'nama_wali'             => 'Nama Wali',
        'jk_wali'               => 'Gender Wali',
        'tmpt_lahir_wali'       => 'Tempat Lahir Wali',
        'tgl_lahir_wali'        => 'Tgl Lahir Wali',
        'agama_wali'            => 'Agama Wali',
        'pekerjaan_wali'        => 'Pekerjaan Wali',
        'alamat_wali'           => 'Alamat Wali',
        'penghasilan_wali'      => 'Penghasilan Wali',
        
        // Status & Berkas Scan Digital
        'status_pendaftaran'    => 'Status Verifikasi',
        'berkas_kk'             => 'File Scan KK',       // Sesuaikan dengan nama kolom tabel upload Anda
        'berkas_akta'           => 'File Scan Akta'      // Sesuaikan dengan nama kolom tabel upload Anda
    ];

    // 5. Cetak baris pertama CSV sebagai Header/Judul Kolom rapi di Excel
    fputcsv($output, array_values($mapping));

    // 6. Tarik seluruh baris data dari database pendaftar
    $query = "SELECT *, id_pendaftar AS id_siswa FROM pendaftar ORDER BY id_pendaftar DESC";
    $stmt = $pdo->query($query);

    // 7. Lakukan perulangan untuk menyusun baris demi baris data pendaftar
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rowData = [];
        
        // Ambil data berdasarkan urutan mapping agar posisinya tidak tertukar di Excel
        foreach ($mapping as $dbKey => $excelLabel) {
            // Jika kolom berkas atau data di database kosong, berikan tanda minus (-)
            $rowData[] = isset($row[$dbKey]) && $row[$dbKey] !== '' ? $row[$dbKey] : '-';
        }
        
        // Tulis baris data pendaftar ke file CSV
        fputcsv($output, $rowData);
    }

    // 8. Tutup koneksi output streaming
    fclose($output);
    exit();

} catch (PDOException $e) {
    // Jika query gagal karena struktur tabel kamu berbeda, tampilkan pesan pelacak error spesifiknya
    die("Gagal Export Data! Terjadi kesalahan pada query SQL: " . $e->getMessage());
}
?>