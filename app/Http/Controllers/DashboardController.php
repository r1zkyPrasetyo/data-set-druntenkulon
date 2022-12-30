<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ResponseRepository;
use App\Models\Bantuan;
use App\Models\Kelompok;
use App\Models\LogSurat;
use App\Models\Penduduk;
use App\Models\PendudukMandiri;
use App\Models\Rtm;
use App\Models\twebKeluarga;
use App\Models\Wilayah;
use Symfony\Component\HttpFoundation\Response;


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
                'penduduk'    => Penduduk::status()->count(),
                'keluarga'    => twebKeluarga::status()->count(),
                'rtm'         => Rtm::status()->count(),
                'kelompok'    => Kelompok::status()->tipe()->count(),
                'dusun'       => Wilayah::dusun()->count(),
                'pendaftaran' => PendudukMandiri::status()->count(),
                'surat'       => LogSurat::count(),
            ];

            return $this->responseRepository->ResponseSuccess($data, 'List Fetch Successfully !');
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
