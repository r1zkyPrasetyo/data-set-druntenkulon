<?php

namespace App\Http\Controllers;

use App\Http\Repositories\ResponseRepository;
use App\Models\Bantuan;
use App\Models\BukuTamu;
use App\Models\IbuHamil;
use App\Models\Kelompok;
use App\Models\LogSurat;
use App\Models\Pamong;
use App\Models\Penduduk;
use App\Models\PendudukMandiri;
use App\Models\PermohonanSurat;
use App\Models\Rtm;
use App\Models\twebKeluarga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;
use DataTables;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;


class DashboardController extends Controller
{
    public function __construct(ResponseRepository $rp)
    {
        $this->responseRepository  = $rp;
    }
    public function index()
    {
        try {
            if (env('STATUS_LANGGANAN_OPENSID') == 'premium') {
                $data = [
                    'penduduk'              => Penduduk::status()->count(),
                    'male'                  => Penduduk::male()->where('status_dasar',1)->count(),
                    'female'                => Penduduk::female()->where('status_dasar',1)->count(),
                    'keluarga'              => twebKeluarga::status()->count(),
                    'rtm'                   => Rtm::status()->count(),
                    'kelompok'              => Kelompok::status()->tipe()->count(),
                    'dusun'                 => Wilayah::dusun()->count(),
                    'pendaftaran'           => PendudukMandiri::status()->count(),
                    'surattercetak'         => LogSurat::count(),
                    'surattercetakharini'   => LogSurat::where('tanggal',Carbon::now())->count(),
                    'ibuhamil'              => IbuHamil::status()->count(),
                    'rekapbukutamu'         => BukuTamu::count(),
                    'statuspermohonansurat'       => [
                        'belumlengkap' => PermohonanSurat::where('status',0)->count(),
                        'sedangdiperiksa' => PermohonanSurat::where('status',1)->count(),
                        'menunggutandatangan' => PermohonanSurat::where('status',2)->count(),
                        'siapdiambil' => PermohonanSurat::where('status',3)->count(),
                        'sudahdiambil' => PermohonanSurat::where('status',4)->count(),
                        'dibatalkan' => PermohonanSurat::where('status',5)->count(),
                    ],
                    'totalpermohonansurat' => PermohonanSurat::count(),
                    'infokepalaDesa' => $this->infoKepalaDesa()
                ];
            }else{
                $data = [
                    'penduduk'              => Penduduk::status()->count(),
                    'male'                  => Penduduk::male()->where('status_dasar',1)->count(),
                    'female'                => Penduduk::female()->where('status_dasar',1)->count(),
                    'keluarga'              => twebKeluarga::status()->count(),
                    'rtm'                   => Rtm::status()->count(),
                    'kelompok'              => Kelompok::status()->tipe()->count(),
                    'dusun'                 => Wilayah::dusun()->count(),
                    'pendaftaran'           => PendudukMandiri::status()->count(),
                    'surattercetak'         => LogSurat::count(),
                    'surattercetakharini'   => LogSurat::where('tanggal',Carbon::now())->count(),
                    'statuspermohonansurat'       => [
                        'belumlengkap' => PermohonanSurat::where('status',0)->count(),
                        'sedangdiperiksa' => PermohonanSurat::where('status',1)->count(),
                        'menunggutandatangan' => PermohonanSurat::where('status',2)->count(),
                        'siapdiambil' => PermohonanSurat::where('status',3)->count(),
                        'sudahdiambil' => PermohonanSurat::where('status',4)->count(),
                        'dibatalkan' => PermohonanSurat::where('status',5)->count(),
                    ],
                    'totalpermohonansurat' => PermohonanSurat::count(),
                    'infokepalaDesa' => $this->infoKepalaDesa()
                ];
            }

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
            ->orderBy('permohonan_surat.created_at','desc');
            return DataTables::eloquent($daftarPermohonanSurat)->toJson();
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logSurat()
    {
        try {
            $logSurat = LogSurat::leftJoin('tweb_surat_format', function ($join) {
                $join->on('log_surat.id_format_surat', '=', 'tweb_surat_format.id');
            })->leftJoin('tweb_penduduk', function ($join) {
                $join->on('log_surat.id_pend', '=', 'tweb_penduduk.id');
            })
            ->select(
                'log_surat.id',
                'log_surat.keterangan',
                'tweb_surat_format.nama as jenis_surat',
                'tweb_penduduk.nama as nama_penduduk',
                'log_surat.tanggal AS tgl_terdaftar'
            )
            ->orderBy('log_surat.tanggal','desc');
            return DataTables::eloquent($logSurat)->toJson();
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function infoKepalaDesa()
    {
        try {
            if (env('STATUS_LANGGANAN_OPENSID') == 'premium') {
                $data = Pamong::status()->leftJoin('ref_jabatan', function ($join) {
                    $join->on('tweb_desa_pamong.jabatan_id', '=', 'ref_jabatan.id');
                })
                ->leftJoin('tweb_penduduk', function ($join) {
                    $join->on('tweb_desa_pamong.id_pend', '=', 'tweb_penduduk.id');
                })
                ->where('tweb_desa_pamong.jabatan_id','1')
                ->select('tweb_penduduk.nama','tweb_penduduk.foto','ref_jabatan.nama as jabatan','tweb_desa_pamong.pamong_masajab','tweb_desa_pamong.gelar_belakang')
                ->first();
           }else{
            $data = Pamong::status()->leftJoin('ref_jabatan', function ($join) {
                $join->on('tweb_desa_pamong.jabatan_id', '=', 'ref_jabatan.id');
            })
            ->leftJoin('tweb_penduduk', function ($join) {
                $join->on('tweb_desa_pamong.id_pend', '=', 'tweb_penduduk.id');
            })
            ->where('tweb_desa_pamong.jabatan_id','1')
            ->select('tweb_desa_pamong.pamong_nama as nama','tweb_desa_pamong.foto','ref_jabatan.nama as jabatan','tweb_desa_pamong.pamong_masajab','tweb_desa_pamong.gelar_belakang')
            ->first();
        }
        return $data;
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function apiScoreSdgs($village_code)
    {

        /** convert code village  3212030010 */
        if (!is_null($village_code)) {
            $codeVillage = $village_code;
        } else {
            $codeVillage = $village_code;           
        }
        try {
            $client = new Client(); //GuzzleHttp\Client //https://sid.kemendesa.go.id/sdgs/searching/score-sdgs?location_code={$kode_desa}
            $url =  "https://sid.kemendesa.go.id/sdgs/searching/score-sdgs?location_code=" . $codeVillage;
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept'     => 'application/json',
                ],
                'verify'  => false,
            ]);

            $decodeJson = json_decode($response->getBody());
            return $decodeJson;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == '400') {
                    return abort(400, 'Maaf Data tidak bisa diambil dari sumber server idm.kemendesa.go.id');
                }
            }
            // You can check for whatever error status code you need 
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(500, 'tidak terhubung dengan server idm.kemendesa.go.id', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function detailSdgs($village_code, $goals)
    {
        if (!is_null($village_code)) {
            $codeVillage = $village_code;
        } else {
            $codeVillage = $village_code;           
        }
        try {
            $client = new Client(); //GuzzleHttp\Client //https://sid.kemendesa.go.id/sdgs/searching/detail-score-sdgs?location_code=3212030010&goals=2
            $url =  "https://sid.kemendesa.go.id/sdgs/searching/detail-score-sdgs?location_code=" . $codeVillage . "&goals=" . $goals;
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Accept'     => 'application/json',
                ],
                'verify'  => false,
            ]);

            $decodeJson = json_decode($response->getBody());
            return $decodeJson;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                if ($e->getResponse()->getStatusCode() == '400') {
                    return abort(400, 'Maaf Data tidak bisa diambil dari sumber server idm.kemendesa.go.id');
                }
            }
            // You can check for whatever error status code you need 
        } catch (\Exception $e) {
            return $this->responseRepository->ResponseError(500, 'tidak terhubung dengan server idm.kemendesa.go.id', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
