<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Harga;
use App\Models\Legalisasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function index()
  {
    return view('dashboard.user.index')->with([
      'user' => Auth::user(),
    ]);
  }

  public function legalisasi_user()
  {
    $data = Legalisasi::where('nim_mahasiswa', Auth::user()->nim)->orderBy('is_upload', 'ASC')->orderBy('is_confirm', 'ASC')->get();
    $ijazah = Harga::all();
    $alamat = Alamat::all();
    return view('dashboard.user.legalisasi_user', compact(['data']))->with([
      'user' => Auth::user(),
      'ijazah' => $ijazah,
      'alamat' => $alamat,
    ]);
  }
  public function tambah_legalisasi_user()
  {
    $ijazah = Harga::where('id', 1)->first();
    $transkrip_nilai = Harga::where('id', 2)->first();
    return view('dashboard.user.tambah_legalisasi')->with([
      'user' => Auth::user(),
      'ijazah' => $ijazah,
      'transkrip_nilai' => $transkrip_nilai,
    ]);
  }

  public function simpan(Request $request)
  {
    $nama = $request->nama;
    $nim = $request->nim;
    $jenis_berkas = $request->berkas;
    $jumlah = $request->jumlah;
    $check = $request->check;
    $pos = $request->pos;
    $alamat = $request->alamat;
    $biaya = $request->biaya;
    $tarif = $request->tarif;
    $catatan = $request->catatan;

    $total = $tarif + $biaya;

    $request->validate(
      [
        'files' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',
        'berkas' => 'required',
        'jumlah' => 'required',
        'check' => 'required',
        'biaya' => 'required',
      ],
      [
        'files.required' => 'File Tidak Boleh Kosong',
        'files.max' => 'File Tidak Boleh Lebih Dari 2MB',
        'files.mimes' => 'Format File Harus JPG,PNG,PDF',
        'berkas.required' => 'Berkas Tidak Boleh Kosong',
        'jumlah.required' => 'Jumlah Tidak Boleh Kosong',
        'check.required' => 'Option Tidak Boleh Kosong',
        'biaya.required' => 'Biaya Tidak Boleh 0',
      ]
    );

    if ($request->hasFile('files')) {
      $custom_file_name = $nama . '-' . $request->file('files')->getClientOriginalName();
      $path = $request->file('files')->storeAs('uploads', $custom_file_name);
    } else {
      $path = '';
    }

    $legalisasi = new Legalisasi;
    $legalisasi->nama_mahasiswa = $nama;
    $legalisasi->nim_mahasiswa = $nim;
    $legalisasi->file_berkas = $path;
    $legalisasi->jumlah = $jumlah;
    $legalisasi->jenis_dokumen = $jenis_berkas;
    $legalisasi->pengambilan = $check;
    $legalisasi->kode_pos = $pos;
    $legalisasi->alamat = $alamat;
    $legalisasi->jumlah_bayar = $total;
    $legalisasi->catatan = $catatan;
    $legalisasi->save();

    $request->session()->flash('msg', "Data Berhasil Disimpan");
    return back()->withErrors([
      'Form' => 'Maaf form tidak boleh ada yang kosong',
    ]);
    return redirect('/tambah_legalisasi');
  }
  public function upload_konfirmasi(Request $request)
  {
    $id = $request->id;
    $nama = $request->nama;

    $request->validate(
      [
        'bukti' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',

      ],
      [
        'bukti.required' => 'File Tidak Boleh Kosong',
        'bukti.max' => 'File Tidak Boleh Lebih Dari 2MB',
        'bukti.mimes' => 'Format File Harus JPG,PNG,PDF',
      ]
    );

    if ($request->hasFile('bukti')) {
      $custom_file_name = $nama . '-' . $request->file('bukti')->getClientOriginalName();
      $path = $request->file('bukti')->storeAs('uploads/konfirmasi', $custom_file_name);
    } else {
      $path = '';
    }

    $legalisasi = Legalisasi::find($id);
    $pathFile = $legalisasi->file_konfirmasi;

    if ($pathFile != null || $pathFile != '') {
      Storage::delete($pathFile);
    }
    $legalisasi->file_konfirmasi = $path;
    $legalisasi->is_upload = 1;
    $legalisasi->save();

    $request->session()->flash('msg', "Berhasil Menyimpan Konfirmasi Pembayaran");
    return back()->withErrors([
      'Form' => 'Maaf form tidak boleh ada yang kosong',
    ]);
    return redirect('/legalisasi');
  }

  public function lihat_bukti(Request $request)
  {
    $url = $request->urlbukti;
    $pdf = public_path("storage/" . $url);
    return response()->download($pdf);
  }
  public function lihat(Request $request)
  {
    $url = $request->urlbukti;
    $format = pathinfo($url, PATHINFO_EXTENSION);
    if ($format == 'pdf') {
      $pdf = public_path("storage/" . $url);
      return response()->download($pdf);
    } else {
      return view('dashboard.user.lihat_bukti')->with([
        'url' => $url,
        'format' => $format
      ]);
    }
  }
  public function lihat_profile(Request $request)
  {
    return view('dashboard.user.profile_user')->with([
      'user' => Auth::user(),
    ]);
  }

  public function update_profile(Request $request)
  {
    $id = $request->id;

    $request->validate(
      [
        'profile' => 'required|mimes:jpeg,png,jpg,pdf|max:2048',

      ],
      [
        'profile.required' => 'File Tidak Boleh Kosong',
        'profile.max' => 'File Tidak Boleh Lebih Dari 2MB',
        'profile.mimes' => 'Format File Harus JPG,PNG,PDF',
      ]
    );

    if ($request->hasFile('profile')) {
      $path = $request->file('profile')->store('profiles');
    } else {
      $path = '';
    }

    $users = User::find($id);
    $pathFile = $users->profile;

    if ($pathFile != null || $pathFile != '') {
      if ($pathFile != 'profiles/default.jpg') {
        Storage::delete($pathFile);
      }
    }
    $users->profile = $path;
    $users->save();

    $request->session()->flash('msg', "Berhasil Update Profile");
    return back()->withErrors([
      'Form' => 'Maaf file tidak boleh ada yang kosong',
    ]);
    return redirect('/legalisasi');
  }

  public function hapus_profile($id)
  {
    $path = 'profiles/default.jpg';

    $users = User::find($id);
    $pathFile = $users->profile;
    if ($pathFile != null || $pathFile != '') {
      if ($pathFile != 'profiles/default.jpg') {
        Storage::delete($pathFile);
      }
    }
    $users->profile = $path;
    $users->save();

    return redirect('/myprofile');
  }
}
