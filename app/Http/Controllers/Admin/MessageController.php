<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages', ['messages' => ContactMessage::latest()->paginate(20)]);
    }

    public function read(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return back();
    }
}
