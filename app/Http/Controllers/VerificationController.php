<?php

namespace App\Http\Controllers;

use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function __construct(private readonly CertificateService $certificates) {}

    public function form(): View
    {
        return view('certificates.verify.form');
    }

    public function submit(Request $request): RedirectResponse
    {
        $data = $request->validate(['code' => ['required', 'string', 'max:100']]);
        return redirect()->route('verify.show', ['code' => trim($data['code'])]);
    }

    public function show(string $code, Request $request): View
    {
        $cert = $this->certificates->verify($code, $request);
        return view('certificates.verify.result', ['certificate' => $cert, 'code' => $code]);
    }
}
