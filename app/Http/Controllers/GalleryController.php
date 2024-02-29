<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $gallery = Gallery::where('user_id', $user->id)->latest()->get();
        return view('index', compact('gallery'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $val = $request->validate([
            'judul'=>'required',
            'deskripsi'=>'required',
            'photo'=>'required|mimes:png,jpg,svg,gif',
        ]);

        if ($request->hasFile('photo')) {
            $filePath = Storage::disk('public')->put('images/posts/', request()->file('photo'));
            $val['photo'] = $filePath;
        }

        $create = Gallery::create([
            'judul'=>$val['judul'],
            'deskripsi'=>$val['deskripsi'],
            'photo'=>$val['photo'],
            'user_id'=>Auth::user()->id,
        ]);

        if ($create) {
            return redirect('/gallery')->with('alert.tambah', 'Photo berhasil diunggah!!');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(Gallery $gallery)
    {
        $gallery->delete();
        return redirect('/gallery')->with('alert.hapus', 'Photo berhasil dihapus!!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gallery $gallery)
    {
        if ($request->hasFile('photo')) {
            $filePath = Storage::disk('public')->put('images/posts/', request()->file('photo'));

            $gallery->judul = $request->judul;
            $gallery->deskripsi = $request->deskripsi;
            $gallery->photo = $filePath;

            $gallery->save();
        } else {
            $gallery->judul = $request->judul;
            $gallery->deskripsi = $request->deskripsi;
            $gallery->photo = $gallery->photo;

            $gallery->save();
        }

        return redirect('/gallery')->with('alert.ubah', 'Data berhasil diubah!!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gallery $gallery)
    {
        //
    }

    
}
