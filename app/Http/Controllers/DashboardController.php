<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ResponseRepository;
use App\Models\Bantuan;
use App\Models\IbuHamil;
use App\Models\Kelompok;
use App\Models\LogSurat;
use App\Models\Penduduk;
use App\Models\PendudukMandiri;
use App\Models\PermohonanSurat;
use App\Models\Rtm;
use App\Models\twebKeluarga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use DataTables;


class DashboardController extends Controller
{
    public function __construct(ResponseRepository $rp)
    {
        $this->responseRepository  = $rp;
    }
    public function index()
    {
        try {
            $data = [
                'penduduk'              => Penduduk::status()->count(),
                'keluarga'              => twebKeluarga::status()->count(),
                'rtm'                   => Rtm::status()->count(),
                'kelompok'              => Kelompok::status()->tipe()->count(),
                'dusun'                 => Wilayah::dusun()->count(),
                'pendaftaran'           => PendudukMandiri::status()->count(),
                'surattercetak'         => LogSurat::count(),
                'surattercetakharini'   => LogSurat::where('tanggal',Carbon::now())->count(),
                'ibuhamil'              => IbuHamil::status()->count(),
                'statuspermohonansurat'       => [
                    'Belum Lengkap' => PermohonanSurat::where('status',0)->count(),
                    'Sedang Diperiksa' => PermohonanSurat::where('status',1)->count(),
                    'Menunggu Tandatangan' => PermohonanSurat::where('status',2)->count(),
                    'Siap Diambil' => PermohonanSurat::where('status',3)->count(),
                    'Sudah Diambil' => PermohonanSurat::where('status',4)->count(),
                    'Dibatalkan' => PermohonanSurat::where('status',5)->count(),
                ],
                'totalpermohonansurat' => PermohonanSurat::count()
            ];

            return $this->responseRepository->ResponseSuccess($data, 'List Fetch Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function daftarPermohonanSurat()
    {
        try {
            $daftarPermohonanSurat = PermohonanSurat::leftJoin('tweb_surat_format', function ($join) {
                $join->on('permohonan_surat.id_surat', '=', 'tweb_surat_format.id');
            })->leftJoin('tweb_penduduk', function ($join) {
                $join->on('permohonan_surat.id_pemohon', '=', 'tweb_penduduk.id');
            })
            ->select(
                'permohonan_surat.id',
                'permohonan_surat.status',
                'permohonan_surat.no_antrian',
                'tweb_surat_format.nama as jenis_surat',
                'tweb_penduduk.nama as nama_penduduk',
                'permohonan_surat.created_at AS tgl_terdaftar'
            )
            ->orderBy('permohonan_surat.id');
            return DataTables::eloquent($daftarPermohonanSurat)->toJson();
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}