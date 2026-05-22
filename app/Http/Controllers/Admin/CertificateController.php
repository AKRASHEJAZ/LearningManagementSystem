<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CertificateActionRequest;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        $q = trim($request->string('q')->toString());

        $query = Certificate::query()
            ->with(['user:id,name,email', 'course:id,title'])
            ->orderByDesc('issued_at')
            ->orderByDesc('id');

        if (in_array($status, ['active', 'revoked'], true)) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub
                    ->whereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('course', function ($c) use ($q) {
                        $c->where('title', 'like', "%{$q}%");
                    })
                    ->orWhere('uuid', 'like', "%{$q}%")
                    ->orWhere('certificate_number', 'like', "%{$q}%");
            });
        }

        $certificates = $query->paginate(30)->withQueryString();

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'status' => $status,
            'q' => $q,
        ]);
    }

    public function revoke(CertificateActionRequest $request, Certificate $certificate)
    {
        if (($certificate->status ?? 'active') === 'revoked') {
            return redirect()->back()->with('status', 'Certificate already revoked.');
        }

        $certificate->forceFill([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by' => $request->user()->id,
            'revocation_reason' => $request->validated('reason'),
        ])->save();

        return redirect()->back()->with('status', 'Certificate revoked.');
    }

    public function reactivate(CertificateActionRequest $request, Certificate $certificate)
    {
        if (($certificate->status ?? 'active') !== 'revoked') {
            return redirect()->back()->with('status', 'Certificate is already active.');
        }

        $certificate->forceFill([
            'status' => 'active',
            'revoked_at' => null,
            'revoked_by' => null,
            'revocation_reason' => null,
        ])->save();

        return redirect()->back()->with('status', 'Certificate reactivated.');
    }

    public function regenerate(CertificateActionRequest $request, Certificate $certificate)
    {
        if ($certificate->pdf_path) {
            try {
                Storage::disk('public')->delete($certificate->pdf_path);
            } catch (\Throwable) {
                // no-op
            }
        }

        $certificate->forceFill(['pdf_path' => null])->save();

        return redirect()->back()->with('status', 'PDF cache cleared. Next download will regenerate.');
    }
}

