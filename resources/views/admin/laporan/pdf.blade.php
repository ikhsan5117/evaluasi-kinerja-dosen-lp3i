<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi Kinerja Dosen LP3I</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            color: #1F2937;
            margin: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1E3A5F;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .header h1 {
            font-size: 14px;
            color: #1E3A5F;
            margin: 0 0 3px;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 12px;
            color: #374151;
            margin: 0 0 3px;
            font-weight: normal;
        }
        .header p {
            font-size: 9px;
            color: #6B7280;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5px;
        }
        .meta-table td {
            padding: 2px 0;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        table.data th {
            background-color: #1E3A5F;
            color: #FFFFFF;
            font-weight: bold;
            text-align: left;
            padding: 6px 5px;
            border: 1px solid #1E3A5F;
        }
        table.data td {
            padding: 5px;
            border: 1px solid #D1D5DB;
        }
        table.data tr:nth-child(even) {
            background-color: #F9FAFB;
        }
        .text-center { text-align: center; }
        .footer {
            margin-top: 25px;
            width: 100%;
        }
        .footer td {
            font-size: 9.5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
            <tr>
                <td style="width: 70px; vertical-align: middle; text-align: left;">
                    @if(file_exists(public_path('images/logo-lp3i.png')))
                        <img src="{{ public_path('images/logo-lp3i.png') }}" style="height: 48px; width: auto;" alt="Logo LP3I">
                    @endif
                </td>
                <td style="vertical-align: middle; text-align: center;">
                    <h1 style="margin: 0; font-size: 15px; color: #1E3A5F; text-transform: uppercase;">LP3I PURWAKARTA</h1>
                    <h2 style="margin: 3px 0 0; font-size: 12px; color: #374151; font-weight: bold;">LAPORAN REKAPITULASI EVALUASI KINERJA DOSEN</h2>
                    <p style="margin: 3px 0 0; font-size: 8.5px; color: #6B7280;">Badan Penjaminan Mutu Akademik &bull; Dicetak pada: {{ date('d F Y, H:i') }} WIB</p>
                </td>
                <td style="width: 70px;"></td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td width="15%"><strong>Periode Akademik</strong></td>
            <td width="35%">: {{ $periode ? $periode->nama_periode : 'Semua Periode' }}</td>
            <td width="15%"><strong>Status Dokumen</strong></td>
            <td width="35%">: Resmi / Final</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="20" class="text-center">No</th>
                <th>Nama Dosen & Gelar</th>
                <th>NIDN</th>
                <th>Program Studi</th>
                <th>Mata Kuliah & Kelas</th>
                <th class="text-center">Responden</th>
                <th class="text-center">Metode Pembelajaran</th>
                <th class="text-center">Profesional</th>
                <th class="text-center">Kepribadian</th>
                <th class="text-center">Sosial</th>
                <th class="text-center">Rata-rata</th>
                <th class="text-center">Predikat</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $idx => $r)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $r['dosen']->nama_lengkap }}</strong></td>
                    <td>{{ $r['dosen']->nidn }}</td>
                    <td>{{ $r['dosen']->programStudi->nama_prodi ?? '-' }}</td>
                    <td>{{ $r['kelas']->mataKuliah->nama_matkul ?? '-' }} ({{ $r['kelas']->nama_kelas }})</td>
                    <td class="text-center">{{ $r['total_responden'] }}</td>
                    <td class="text-center">{{ number_format($r['pedagogik'], 2) }}</td>
                    <td class="text-center">{{ number_format($r['profesional'], 2) }}</td>
                    <td class="text-center">{{ number_format($r['kepribadian'], 2) }}</td>
                    <td class="text-center">{{ number_format($r['sosial'], 2) }}</td>
                    <td class="text-center"><strong>{{ number_format($r['rata_rata'], 2) }}</strong></td>
                    <td class="text-center">{{ $r['predikat'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td width="60%"></td>
            <td width="40%" style="text-align: center;">
                Mengetahui,<br>
                <strong>Kepala Bagian Akademik & Mutu LP3I</strong><br><br><br><br>
                ( ___________________________ )
            </td>
        </tr>
    </table>
</body>
</html>
