<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FotoAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

// use File;

class FotoAdminController extends Controller
{
    public function create()
    {
        return view('admin.foto.add');
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'judul' => 'required',
            'konten' => 'required',
            'caption' => 'required',
        ]);
        // dd($request->konten);

        // foreach (array_filter($request->konten, 'strlen') as $item => $value) {
            
        //     $extFoto[$item] = $request->konten[$item]->getClientOriginalExtension();
        //     $pathFoto[$item] = "foto-".time().".".$extFoto[$item];
        //     $pathStore[$item] = $request->konten[$item]->move(public_path('fotoProd/'), $pathFoto[$item]);
        //     $gambar = array(
        //         'judul' => $request->judul,
        //         'konten' => $pathFoto[$item],
        //         'caption' => $request->caption
        //     );            

        //     FotoAdmin::insert($gambar);
        //     // dd($gambar);
        // }

        $files = [];
        foreach ($request->file('konten') as $file) {
            if ($file->isValid()) {
                $konten = round(microtime(true) * 1000).'-'.str_replace(' ','-',$file->getClientOriginalName());
                $file->move(public_path('../fotoProd/'), $konten);                    
                $files[] = [
                    'judul' => $request->judul,
                    'konten' => $konten,
                    'caption' => $request->caption
                ];
            }
        }
        FotoAdmin::insert($files);

        // FotoAdmin::create([
        //     "judul" => $request["judul"],
        //     "konten" => $pathFoto,
        //     "caption" => $request["caption"],
        // ]);

        return redirect('/admin/list-foto')->with('success', 'Foto Berhasil Ditambahkan!');
    }

    public function index()
    {
        $fotos = FotoAdmin::groupBy('judul')->get();
        // dd($fotos);
        return view('admin.foto.list', compact('fotos'));
    }

    // public function show($id) {
    //     $submission = DB::table('submission')->where('id', $id)->first();
    //     return view('admin.show',compact('submission'));
    // }

    public function edit($id) {
        $foto = FotoAdmin::find($id)->first();
        return view('admin.foto.edit',compact('foto'));
    }

    public function update($id, Request $request) {
        $request->validate([
            'judul' => 'required',
            'konten' => 'mimes:jpeg,jpg,png|max:2200', 
            'caption' => 'required'
        ]);

        $foto = FotoAdmin::findorfail($id);
        if ($request->has('picture')) {
            File::delete("fotoProd//".$foto->picture);
            $picture = $request->picture;
            $pathFoto = time() . ' - ' . $picture->getClientOriginalName();
            $picture->move('fotoProd//', $pathFoto);
/*
            $request->has('konten_picture')) {
            File::delete("fotoProd//".$foto->konten_picture);
            $konten_picture = $request->konten_picture;
            $new_konten = time() . ' - ' . $konten_picture->getClientOriginalName();
            $konten_picture->move('fotoProd//', $new_foto_sampul);
*/
            $foto_data = [
                "judul" => $request["judul"],
                "konten" => $pathFoto,
                "caption" => $request["caption"],
            ];
        } else {
            $foto_data = [
                "judul" => $request["judul"],
                //"konten" => $pathFoto,
                "caption" => $request["caption"]
            ];
        }
        
        $foto->update($foto_data);

        return redirect('/admin/list-foto')->with('success', 'Foto Berhasil Diupdate!');
    }

    public function destroy($id) {
        $submission = DB::table('fotos')->where('id', $id)->delete();
        return redirect('/admin/list-foto')->with('success', 'Foto Berhasil Dihapus!');
    }
/*
    public function upload($id) {
        $foto = Foto::where('id',$id)->first();
        return view('layouts.foto_content',compact('article')); //belum ditambahin ke layout
    }

    public function list_content() {
        $fotos = Foto::all();
        return view('layouts.foto',compact('fotos')); 
    }
*/
}
