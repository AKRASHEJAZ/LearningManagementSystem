<?php

namespace App\Http\Controllers;

use App\Models\Certificate;

class MyCertificatesController extends Controller
{
    public function index()
    {
        $user = request()->user();
        abort_unless($user, 403);

        $certificates = Certificate::query()
            ->with(['course:id,title,slug'])
            ->where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('certificates.mine.index', [
            'certificates' => $certificates,
        ]);
    }
}

