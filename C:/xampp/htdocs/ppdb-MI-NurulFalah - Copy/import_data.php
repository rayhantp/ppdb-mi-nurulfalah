<?php
include 'config/database.php';

// Data siswa dikelompokkan per kelas dengan tahun pendaftaran yang masuk akal:
// Kelas 1 = masuk 2025/2026 (TA 2025/2026)
// Kelas 2 = masuk 2024/2025
// Kelas 3 = masuk 2023/2024
// Kelas 4 = masuk 2022/2023
// Kelas 5 = masuk 2021/2022

$data_per_kelas = [
    // ============ KELAS 1 ============
    '1A' => [
        'tahun_ajaran' => '2025/2026',
        'tgl_daftar_start' => '2025-05-01',
        'tgl_daftar_end'   => '2025-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['ADZKIA MARIANA AZHAR', '2019-02-20', 'P'],
            ['AFIFAH ANINDYA RIZKI', '2019-01-08', 'P'],
            ['AHMAD GHANI ALFATIH', '2018-08-09', 'L'],
            ['AYRA SIRLI AL NAYRA', '2018-06-27', 'P'],
            ['FAQIH AL HAFIZ', '2017-02-02', 'L'],
            ['MUHAMAD ADDAR QUTHNY', '2019-01-12', 'L'],
            ['MUHAMMAD ALWI ALFIANSYAH', '2018-09-10', 'L'],
            ['MUHAMMAD FAIZ ABBASY', '2019-01-12', 'L'],
            ['MUHAMMAD INAYATULLAH', '2017-03-29', 'L'],
            ['MUHAMMAD ARSYA ALFARIZQI', '2018-08-06', 'L'],
            ['MUHAMAD ABIY VISHAKA', '2019-01-07', 'L'],
            ['MUHAMMAD NIZHAM AL-FATIH', '2018-06-12', 'L'],
            ['NADIA AMIRAH SOLEH', '2018-03-10', 'P'],
            ['RATU SHEZA ALFATHUNISA', '2018-01-08', 'P'],
            ['RIKE DAMAYANTI', '2018-12-18', 'P'],
            ['SANDHYA RAMADANI', '2019-05-28', 'P'],
            ['SHOFIA LUBNA NUSHOIBAH', '2018-12-03', 'P'],
            ['YUMNA KHAIRUNNISA', '2018-08-04', 'P'],
            ['MARVEL FEBRIANSYAH', '2019-02-02', 'L'],
        ]
    ],
    '1B' => [
        'tahun_ajaran' => '2025/2026',
        'tgl_daftar_start' => '2025-05-01',
        'tgl_daftar_end'   => '2025-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['ABIMANA SEHAN SETIAWAN', '2018-02-26', 'L'],
            ['AYESHA SHANUM', '2019-05-10', 'P'],
            ['ANNISA SALSABILLAH', '2018-08-31', 'P'],
            ['AIDA NUR SAFA', '2018-06-25', 'P'],
            ['AZKA RAMADHAN', '2018-06-01', 'L'],
            ['FARHANA NUR FATIHAH', '2018-08-31', 'P'],
            ['FATIMAH HAFIZAH QISTY', '2019-02-08', 'P'],
            ['HANA SYAQILA HUMAIRA', '2018-04-06', 'P'],
            ['HILYA BEAUTY AKHLAQI', '2018-10-08', 'P'],
            ['IFFAH MAULIDINA RANI KHOSI\'IN', '2018-12-03', 'P'],
            ['IRSAL ALAMSYAH TAHIR', '2019-02-28', 'L'],
            ['JASMINE ARSYILA KAMILA', '2018-11-28', 'P'],
            ['JIA ALMAHIYRA', '2018-09-16', 'P'],
            ['NADIRA QURATU AINIA', '2018-08-17', 'P'],
            ['NAFISYA AZZAHRA', '2018-05-03', 'P'],
            ['QUENSYA NAILAL KEINARA', '2018-08-19', 'P'],
            ['AISYAH AJAHIRA', '2018-06-12', 'P'],
        ]
    ],
    '1_tidak_aktif' => [
        'tahun_ajaran' => '2025/2026',
        'tgl_daftar_start' => '2025-05-01',
        'tgl_daftar_end'   => '2025-07-15',
        'status' => 'tidak_aktif',
        'siswa' => [
            ['LAYLA NAZMA HAFIDZAH', '2019-03-03', 'P'],
        ]
    ],

    // ============ KELAS 2 ============
    '2A' => [
        'tahun_ajaran' => '2024/2025',
        'tgl_daftar_start' => '2024-05-01',
        'tgl_daftar_end'   => '2024-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['ABDUL BASITH MUBAROK', '2017-04-06', 'L'],
            ['AHMAD MUGNI AL-FADILLAH', '2018-03-19', 'L'],
            ['CHERYL LAVENIA RAHMAN', '2017-03-28', 'P'],
            ['DJAFAR IBRAHIM', '2017-03-09', 'L'],
            ['FAUZIA NUR SAFIYA', '2017-04-11', 'P'],
            ['FERRO ARGATAMA ZAIDAN', '2018-01-25', 'L'],
            ['KHOIRUNISA SALSABILAH', '2017-11-06', 'P'],
            ['MUHAMAD MUSA', '2017-12-18', 'L'],
            ['MUHAMMAD ADNAN SAPUTRA', '2017-10-28', 'L'],
            ['MUHAMMAD AZZAM ALIUDDIN', '2018-03-20', 'L'],
            ['RANI ENJELIKA PUTRI', '2017-07-15', 'P'],
            ['RAYSA RAHMAWATI ROMLI', '2017-07-02', 'P'],
            ['RASYIDATU MARWA APRILIA', '2017-04-01', 'P'],
            ['RIZKYA DEWI PUTRI SETYOWATI', '2018-02-03', 'P'],
            ['SAHLA FAUZIYAH', '2017-06-19', 'P'],
            ['SYAHTI RAFIF AL-FATAH', '2017-08-26', 'L'],
            ['ZAHRA ANAZWA ADITYA', '2017-11-01', 'P'],
            ['MOHAMMAD IFWAN MAULANA', '2018-01-05', 'L'],
            ['FAIREL RAMADHANSYAH', '2017-06-01', 'L'],
        ]
    ],
    '2B' => [
        'tahun_ajaran' => '2024/2025',
        'tgl_daftar_start' => '2024-05-01',
        'tgl_daftar_end'   => '2024-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['ABDAN RASYID AL-FATTAH', '2017-02-27', 'L'],
            ['ABU SALMAN AL-FARIZZI', '2017-03-01', 'L'],
            ['AFIZAH KHAIRUNNISA', '2017-08-11', 'P'],
            ['AHMAD NOVIAN SHAQIL', '2017-11-22', 'L'],
            ['AHMAD RAFAN HAMKA', '2017-05-01', 'L'],
            ['AISYAH NURRAMADANI', '2017-06-18', 'P'],
            ['AISYAH SEPTYANI IRAWAN', '2017-09-24', 'P'],
            ['ALIKA PUTRI LYLAHASYA', '2017-11-07', 'P'],
            ['ALISHA NAHDA RAFANDA', '2018-05-04', 'P'],
            ['ANWAR DZAKI MAKARIM', '2017-04-27', 'L'],
            ['ARAFAHTUS SOLEHA', '2017-08-31', 'P'],
            ['AZKAYRA SHEZAN LUCKIS', '2017-05-01', 'P'],
            ['AZMYA NUR ANNASYA', '2018-03-15', 'P'],
            ['ARSYIFA ANINDITA NURALIM', '2017-08-07', 'P'],
            ['MUHAMMAD KASYFU FIRDAUS', '2017-11-21', 'L'],
            ['MUHAMMAD WILDAN FIRDAUS', '2017-04-08', 'L'],
        ]
    ],

    // ============ KELAS 3 ============
    '3A' => [
        'tahun_ajaran' => '2023/2024',
        'tgl_daftar_start' => '2023-05-01',
        'tgl_daftar_end'   => '2023-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['ALBIANSYAH', '2014-05-18', 'L'],
            ['ANIQ FATHINA RAHMADANA', '2016-06-17', 'P'],
            ['ASYIFA ZAHRA BUDIMAN', '2017-01-06', 'P'],
            ['BILQIS FAIZA ALYA AZIZAH', '2016-02-11', 'P'],
            ['HAVIKA NUR ZAHIRA', '2016-02-10', 'P'],
            ['IHSANI NUR ALFI SYAHRIN', '2016-06-26', 'P'],
            ['MAGHFIRA AZ-HAIRA', '2016-10-10', 'P'],
            ['MUHAMMAD IBNU FIRDAUS', '2016-05-05', 'L'],
            ['MUHAMMAD NUR AZRIL RAHANDIKA', '2016-05-19', 'L'],
            ['MUHAMAD ABDILLAH PRATAMA', '2016-05-18', 'L'],
            ['MUHAMAD AL IQBAL SYAHDAN', '2016-07-24', 'L'],
            ['MUHAMMAD GIBRAN KHADAFI', '2016-02-29', 'L'],
            ['NURAZIZAH KHOIRUNISAH', '2016-03-06', 'P'],
            ['REVALIA NAZMA PUTRI', '2016-03-25', 'P'],
            ['SITI NUR FADILLAH', '2017-07-10', 'P'],
            ['SITI SULISTIAWATI', '2016-10-26', 'P'],
            ['ARSYLLA AISYAH INARA', '2016-11-28', 'P'],
            ['MUHAMMAD ABDULLAH AL GANIY', '2016-08-01', 'L'],
            ['MUHAMMAD ABIZAR IBNU HAFIDZ', '2016-09-14', 'L'],
        ]
    ],
    '3B' => [
        'tahun_ajaran' => '2023/2024',
        'tgl_daftar_start' => '2023-05-01',
        'tgl_daftar_end'   => '2023-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['CELLA DESWITA KHARISMA PUTRI', '2013-12-13', 'P'],
            ['ABDUL MUJIB HAQIQI', '2016-04-09', 'L'],
            ['MUHAMMAD AZZAM KOMARUDIN', '2016-10-22', 'L'],
            ['APRILIA NUR ISTIQOMAH', '2016-04-18', 'P'],
            ['AZZAM AULIA ADHA', '2016-09-12', 'L'],
            ['FAKHRI MAULANA', '2016-02-28', 'L'],
            ['FEBRI WULANDARI', '2017-02-06', 'P'],
            ['HANIFAH AMALIA', '2015-08-10', 'P'],
            ['ISHITA ZAHRA', '2016-11-05', 'P'],
            ['KAYLA EVELYN DALIYA', '2016-09-13', 'P'],
            ['MUAMMAR IBNU FARIS', '2016-10-21', 'L'],
            ['MUHAMAD RIFKY', '2016-09-16', 'L'],
            ['MUHAMAD SYAHDAL AKHYA', '2016-07-24', 'L'],
            ['MUHAMAD ZIKRI RAMADHAN', '2017-05-26', 'L'],
            ['MUHAMMAD RASYAD ALFATAN', '2016-07-20', 'L'],
            ['RAFA AZKA FAEZA', '2016-05-22', 'L'],
            ['RAUDHATUL AISY', '2016-10-21', 'P'],
            ['SHAFANA ZOYA KINARIAN', '2017-05-21', 'P'],
            ['SITI FATIMAHTUL ZAHRO', '2017-02-10', 'P'],
            ['WARDATUL IZZATUN NISA', '2015-12-05', 'P'],
            ['ZAHID UBUDAH', '2016-04-08', 'L'],
            ['MUHAMMAD IQBAL AL-FATTAH', '2016-07-15', 'L'],
        ]
    ],

    // ============ KELAS 4 ============
    '4A' => [
        'tahun_ajaran' => '2022/2023',
        'tgl_daftar_start' => '2022-05-01',
        'tgl_daftar_end'   => '2022-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['MUHAMAD RADITYA OKTAVIANO', '2015-10-15', 'L'],
            ['MUHAMMAD ALBI SYAHRIL TUANANY', '2015-07-15', 'L'],
            ['SALMA AULIA ZAFIRA', '2016-03-26', 'P'],
            ['MUHAMAD RIZKI', '2015-11-26', 'L'],
            ['MUHAMMAD FADHLI TSANY', '2015-12-24', 'L'],
            ['MUHAMMAD RAFFA AZKA PUTRA', '2015-07-31', 'L'],
            ['MUHAMMAD PUTRA ALFIANSYAH', '2015-03-27', 'L'],
            ['MALIK AHMAD AL-AZAMI', '2016-04-02', 'L'],
            ['MEIKA SILHA', '2016-05-06', 'P'],
            ['MUHAMAD ARIQ AL ABASSY', '2015-10-05', 'L'],
            ['SHAVIRA SHALIHAH', '2016-05-05', 'P'],
            ['MUHAMMAD NAUFAL ABYYU', '2015-12-17', 'L'],
            ['MUHAMMAD RAFA AZKA PUTRA', '2015-08-19', 'L'],
            ['MUFIDA ZARA SALSABILA', '2015-01-20', 'P'],
            ['MUHAMAD RAFA AZKA PUTRA', '2015-02-04', 'L'],
            ['MUHAMAD RIZKY FADHILLAH', '2015-10-16', 'L'],
            ['RAKHSANDIRA KAYSA SYIFA', '2016-01-16', 'P'],
            ['MUHAMMAD RIZAL BAKTIAR', '2014-06-03', 'L'],
        ]
    ],
    '4B' => [
        'tahun_ajaran' => '2022/2023',
        'tgl_daftar_start' => '2022-05-01',
        'tgl_daftar_end'   => '2022-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['FATIHA ZAHIDA', '2016-03-09', 'P'],
            ['FEBRI RIYAN HERMAWAN', '2015-02-21', 'L'],
            ['HILYATUS SHOLEHAH', '2015-08-11', 'P'],
            ['HISYAM ALIM ZUHDY', '2015-02-21', 'L'],
            ['FAQIHA FASHIHATUNNISA', '2016-01-27', 'P'],
            ['ELFANI MUSFIROH', '2015-03-14', 'P'],
            ['ADZKIYA SAHLA IZZATUNNISA', '2015-10-18', 'P'],
            ['DIMAS JORDY', '2015-05-01', 'L'],
            ['BRIANAH NUR KHALISAH', '2015-02-08', 'P'],
            ['ALIKA NAILA PUTRI', '2016-01-01', 'P'],
            ['AL GHIFARI AZHAR', '2016-01-30', 'L'],
            ['AISYAH NUR LATIFAH', '2016-04-06', 'P'],
            ['AIDIL FAHMI NASUTION', '2014-08-23', 'L'],
            ['AHMAD FAUZI TAHIR', '2015-11-10', 'L'],
            ['ADITYA NAVAN DANISH', '2016-01-21', 'L'],
            ['ADIBA SYAKIRA', '2015-09-29', 'P'],
            ['HAFIZ SAIPUL ULUM', '2015-05-29', 'L'],
            ['FATHINA UZMA UMAIZA', '2016-01-23', 'P'],
            ['INDANA HAWRA KAHLA', '2016-04-06', 'P'],
            ['KHAYLA DEA OKTAVIANI', '2015-10-06', 'P'],
            ['SUMIYATUL AINI', '2016-02-01', 'P'],
            ['MUHAMMAD AZZAM AL JARAS', '2015-09-04', 'L'],
        ]
    ],

    // ============ KELAS 5 ============
    '5A' => [
        'tahun_ajaran' => '2021/2022',
        'tgl_daftar_start' => '2021-05-01',
        'tgl_daftar_end'   => '2021-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['MUHAMAD ARKA KUSUMA', '2014-08-15', 'L'],
            ['MUHAMAD TOHIRUN', '2014-11-26', 'L'],
            ['MUHAMMAD AFHAM SYAKUR', '2014-11-06', 'L'],
            ['MUHAMMAD DWI RIZKY', '2014-11-01', 'L'],
            ['MUHAMMAD NAUFAL AFKAR', '2014-10-21', 'L'],
            ['MUHAMMAD NIZAR AN-NAFI', '2014-03-06', 'L'],
            ['NUR DZAKIYAH TALITA', '2014-12-29', 'P'],
            ['NURUL FAHRIZI', '2013-11-30', 'L'],
            ['RAZIQ HANAN', '2014-08-19', 'L'],
            ['RIZKY DAFFI ARDIANSYAH', '2014-12-18', 'L'],
            ['RIZKY ELVIRO', '2014-08-08', 'L'],
            ['SITI KHOIRUNNISA', '2014-03-26', 'P'],
            ['MUHAMMAD UWAIS', '2013-03-29', 'L'],
            ['MUHAMMAD REZKY ADITYA', '2013-02-22', 'L'],
        ]
    ],
    '5B' => [
        'tahun_ajaran' => '2021/2022',
        'tgl_daftar_start' => '2021-05-01',
        'tgl_daftar_end'   => '2021-07-15',
        'status' => 'diterima',
        'siswa' => [
            ['AIRIN NAZILA', '2013-04-26', 'P'],
            ['ADRIAN FARID HUSAINI', '2014-12-23', 'L'],
            ['AHMAD ZULFAN MUBAROK', '2014-05-29', 'L'],
            ['AISYAH NAYYA SAPUTRA', '2014-11-06', 'P'],
            ['ANNISA MUSYARIFAH', '2014-09-11', 'P'],
            ['ARFAN ABDUL HAFIZH', '2015-04-19', 'L'],
            ['AZKHA LEGASY AL UBUDIAH', '2014-08-17', 'P'],
            ['CHAYRAH MARSHA ZAIDA HARTONO', '2014-03-09', 'P'],
            ['DELISHA LULU MUMTAZAH', '2014-11-05', 'P'],
            ['HUZMAH DHAFITHA', '2014-08-04', 'P'],
            ['KHANZA AULIA SALSABILA', '2015-01-19', 'P'],
            ['MAITSAA BILHUSNA MUHDIYYAH', '2014-04-26', 'P'],
            ['MOHAMAD RIZKI GUNAWAN', '2012-11-01', 'L'],
            ['ANNASTASYA RAMADHAN', '2014-07-19', 'P'],
            ['ARSIL MAHIR M. SOLEH', '2013-12-02', 'L'],
            ['YUDISTIRA', '2014-10-20', 'L'],
        ]
    ],
];

$total = 0;
$errors = 0;

foreach ($data_per_kelas as $kelas_key => $kelas_data) {
    $start = strtotime($kelas_data['tgl_daftar_start']);
    $end   = strtotime($kelas_data['tgl_daftar_end']);

    foreach ($kelas_data['siswa'] as $siswa) {
        $nama        = $siswa[0];
        $tgl_lahir   = $siswa[1];
        $jk          = $siswa[2];
        $status      = $kelas_data['status'];
        $tahun_ajaran = $kelas_data['tahun_ajaran'];

        // Generate random tgl_daftar in the range for this class
        $tgl_daftar = date('Y-m-d H:i:s', rand($start, $end));

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO pendaftar (nama_siswa, tanggal_lahir, jenis_kelamin, tgl_daftar, tahun_ajaran, status_pendaftaran) 
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nama, $tgl_lahir, $jk, $tgl_daftar, $tahun_ajaran, $status]);
            $total++;
        } catch (Exception $e) {
            $errors++;
            echo "ERROR insert $nama: " . $e->getMessage() . "<br>";
        }
    }
}

echo "<h2>✅ Import selesai!</h2>";
echo "<p>Berhasil diimpor: <strong>$total siswa</strong></p>";
if ($errors > 0) {
    echo "<p style='color:red'>Gagal: $errors baris</p>";
}
echo "<a href='views/admin/analisis_tren.php'>Lihat Analisis Tren →</a>";
?>
