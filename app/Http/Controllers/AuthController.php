<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index() {
        return view('login');
    }

    public function register() {
        return view('register');
    }

    public function postLogin(Request $request) {
        if (Auth::attempt($request->only('email', 'username', 'password'))) {
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            } else {
                return redirect('/gallery');
            }
            
        } else {
            return back()->with('alert', 'Data ada yang salah!!');
        }

    }

    public function postRegister(Request $request) {
        $email = User::where('email', $request->email)->first();
        if ($email) {
            return back()->with('alert', 'Email sudah terdaftar!!');
        }

        if ($request->password == $request->repassword) {
            $ins = User::create([
                'name'=>$request->name,
                'username'=>$request->username,
                'email'=>$request->email,
                'password'=>bcrypt($request->password),
            ]);

            Auth::login($ins);
            return redirect()->intended('/gallery');
        }

        return back()->with('alert', 'Password tidak sama!!');
    }

    public function logout() {
        Auth::logout();
        return redirect('/');
    }

    public function profile() {
        $user = Auth::user();
        $gallery = Gallery::where('user_id', $user->id)->latest()->get();
        return view('profile', compact('gallery'));
    }

    public function updateUser(Request $request, User $user, $id) {

        // if (isset($request->password)) {
            
            //     User::where('id', $user->id)->update([
                //         'name'=>$request->name,
                //         'username'=>$request->username,
                //         'email'=>$request->email,
                //         // 'role'=>$request->role,
                //         'password'=>bcrypt($request->password),
                //     ]);
                // } else {
                    //     User::where('id', $user->id)->update([
                        //         'name'=>$request->name,
                        //         'username'=>$request->username,
                        //         'email'=>$request->email,
                        //         // 'password'=>bcrypt($request->password),
                        //     ]);
                        // }
                        
                        
            // $email = User::where('email', $request->email)->first();
            // if ($email) {
            //     return back()->with('alert', 'Ubah Gagal, karena Email sudah terdaftar!!');
            // } else {

                $create = User::where('id', $user->id)->validate([
                    'name'=>'required',
                    'username'=>'username',
                    'email'=>'email',
                    'password'=>'password',
                ]);

                if (isset($request->password)) {
                    if ($request->password == $request->repassword) { 

                        if ($create) {
                            User::updated([
                                'name'=>$request->name, 
                                'username'=>$request->username, 
                                'email'=>$request->email, 
                                'password'=>bcrypt($request->password), 
                            ]);
    
                            return redirect('/profile');
                        }
                        // return back()->with('alert', 'Data berhasil diubah!!');
                    } else {
                        return back()->with('alert', 'Password tidak sama, cek kembali kesamaan passsword yang anda masukkan!!');
                    }

                } else {

                    $create = User::updated([
                        'name'=>$request->name, 
                        'username'=>$request->username, 
                        'email'=>$request->email, 
                        // 'password'=>bcrypt($request->password), 
                    ]);

                    if ($create) {
                        return redirect('/profile');
                    }
                    return back()->with('alert', 'Data berhasil diubah!!');
                }

                return back()->with('alert', 'Tidak ada data yang diubah!!');         
            
        }
}