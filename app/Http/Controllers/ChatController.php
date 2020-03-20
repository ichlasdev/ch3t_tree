<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Message;
use Auth;

class ChatController extends Controller
{

    public function __contruct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chat');
    }

    public function fetchMessages(Request $request)
    {
        // MENGAMBIL SEMUA LOG PESAN BESERTA USER YANG MENJADI PEMILIKNYA MENGGUNAKAN EAGER LOADING
        // PEMBAHASAN MENGENAI EAGER LOADING BISA DICARI DI DAENGWEB.ID
        return Message::with('user')->get();
    }

    // FUNGSI UNTUK BROADCAST SERTA MENYIMPAN KE DATABASE
    public function broadcastMessage(Request $request)
    {
        $user = Auth::user(); //AMIL USER YANG SEDANG LOGIN
        //SIMPAN DATA KE TABLE MESSAGES MELALUI USER
        $message = $user->messages()->create([
            'message' => $request->input('message')
        ]);

        // BROADCAST EVENTNYA
        broadcast(new MessageSent($user, $message))->toOthers();
        return response()->json(['status' => 'Message Sent!']);
    }
}
