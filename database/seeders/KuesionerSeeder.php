<?php

namespace Database\Seeders;

use App\Models\Kuesioner;
use App\Models\Periode;
use App\Models\Pertanyaan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KuesionerSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $periode = Periode::where('status', 'Aktif')->first() ?? Periode::latest()->first();

        if (!$periode) {
            $this->command->warn('⚠️  Periode tidak ditemukan untuk KuesionerSeeder.');
            return;
        }

        $kuesioner = Kuesioner::firstOrCreate(
            [
                'judul' => 'Evaluasi Kinerja Dosen 2026',
                'periode_id' => $periode->id,
            ],
            [
                'deskripsi' => 'Kuesioner evaluasi mutu dan kinerja dosen pengampu mata kuliah. Silakan isi dengan jujur dan objektif.',
                'kategori' => 'Evaluasi Kegiatan Mengajar',
                'status' => 'Aktif',
                'created_by' => $admin?->id ?? 1,
            ]
        );

        $pertanyaanList = [
            // METODE PEMBELAJARAN (1-4)
            [
                'nomor_urut' => 1,
                'kategori' => 'Metode Pembelajaran',
                'teks_pertanyaan' => 'Dosen menyampaikan silabus, rencana pembelajaran (RPS), dan kontrak kuliah di awal semester dengan jelas.',
            ],
            [
                'nomor_urut' => 2,
                'kategori' => 'Metode Pembelajaran',
                'teks_pertanyaan' => 'Dosen menguasai metode pembelajaran yang interaktif dan mudah dipahami mahasiswa.',
            ],
            [
                'nomor_urut' => 3,
                'kategori' => 'Metode Pembelajaran',
                'teks_pertanyaan' => 'Dosen memanfaatkan media dan teknologi pembelajaran secara efektif selama perkuliahan.',
            ],
            [
                'nomor_urut' => 4,
                'kategori' => 'Metode Pembelajaran',
                'teks_pertanyaan' => 'Dosen memberikan umpan balik dan evaluasi yang jelas terhadap tugas atau ujian mahasiswa.',
            ],

            // PROFESIONAL (5-8)
            [
                'nomor_urut' => 5,
                'kategori' => 'Profesional',
                'teks_pertanyaan' => 'Dosen menguasai materi perkuliahan secara mendalam dan terstruktur.',
            ],
            [
                'nomor_urut' => 6,
                'kategori' => 'Profesional',
                'teks_pertanyaan' => 'Dosen memberikan contoh nyata dan relevan dengan kebutuhan dunia kerja/industri.',
            ],
            [
                'nomor_urut' => 7,
                'kategori' => 'Profesional',
                'teks_pertanyaan' => 'Dosen mampu menjawab pertanyaan dan memfasilitasi diskusi kelas dengan memuaskan.',
            ],
            [
                'nomor_urut' => 8,
                'kategori' => 'Profesional',
                'teks_pertanyaan' => 'Dosen menyampaikan materi perkuliahan tepat waktu sesuai silabus (RPS).',
            ],

            // KEPRIBADIAN (9-12)
            [
                'nomor_urut' => 9,
                'kategori' => 'Kepribadian',
                'teks_pertanyaan' => 'Dosen hadir tepat waktu dan disiplin sesuai jadwal perkuliahan yang ditentukan.',
            ],
            [
                'nomor_urut' => 10,
                'kategori' => 'Kepribadian',
                'teks_pertanyaan' => 'Dosen berpenampilan rapi, sopan, dan mencerminkan etika keteladanan pendidik.',
            ],
            [
                'nomor_urut' => 11,
                'kategori' => 'Kepribadian',
                'teks_pertanyaan' => 'Dosen bersikap adil, objektif, dan transparan dalam memberikan penilaian.',
            ],
            [
                'nomor_urut' => 12,
                'kategori' => 'Kepribadian',
                'teks_pertanyaan' => 'Dosen memiliki wibawa dan mampu menciptakan suasana kelas yang tertib dan kondusif.',
            ],

            // SOSIAL (13-15)
            [
                'nomor_urut' => 13,
                'kategori' => 'Sosial',
                'teks_pertanyaan' => 'Dosen bersikap ramah, komunikatif, dan mudah dihubungi untuk konsultasi akademik.',
            ],
            [
                'nomor_urut' => 14,
                'kategori' => 'Sosial',
                'teks_pertanyaan' => 'Dosen menghargai perbedaan pendapat dan terbuka terhadap saran dari mahasiswa.',
            ],
            [
                'nomor_urut' => 15,
                'kategori' => 'Sosial',
                'teks_pertanyaan' => 'Dosen mampu membangun hubungan yang saling menghormati dan bersahabat dengan mahasiswa.',
            ],
        ];

        foreach ($pertanyaanList as $item) {
            Pertanyaan::firstOrCreate(
                [
                    'kuesioner_id' => $kuesioner->id,
                    'nomor_urut' => $item['nomor_urut'],
                ],
                [
                    'kategori' => $item['kategori'],
                    'teks_pertanyaan' => $item['teks_pertanyaan'],
                ]
            );
        }

        $this->command->info("✅ Kuesioner seeded: '{$kuesioner->judul}' dengan 15 butir pertanyaan.");
    }
}
