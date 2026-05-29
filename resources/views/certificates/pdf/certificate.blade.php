The reason your layout is spilling onto a second page in DomPDF is due to a combination of **explicit pixel/mm dimensions inside a container flow** and **uncollapsed margins/paddings**. DomPDF treats the container height strictly, and when it runs out of vertical space by even $0.1\text{mm}$, it forcefully creates a second blank or broken page.

To guarantee that it fits **exactly on one A4 landscape page**, we must use an absolute coordinate matrix for major structural blocks while setting precise typography line-heights.

Here is the fully fixed, optimized code. It squeezes the vertical footprint safely inside the $210\text{mm}$ boundary:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificate_number }}</title>
    <style>
        @page { margin: 0; size: A4 landscape; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { width: 297mm; height: 210mm; overflow: hidden; background: #fff; }
        body { font-family: "DejaVu Serif", Georgia, serif; color: #1a1a1a; position: relative; }

        /* ── Security Layer 1: Guilloche radial pattern ── */
        .sec-bg {
            position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                repeating-radial-gradient(circle at 18% 50%, transparent 0, transparent 3.2mm, rgba(178,35,43,.025) 3.2mm, rgba(178,35,43,.025) 3.7mm),
                repeating-radial-gradient(circle at 82% 50%, transparent 0, transparent 3.2mm, rgba(29,53,87,.020) 3.2mm, rgba(29,53,87,.020) 3.7mm),
                repeating-radial-gradient(circle at 50% 18%, transparent 0, transparent 4.5mm, rgba(178,35,43,.015) 4.5mm, rgba(178,35,43,.015) 5mm),
                repeating-radial-gradient(circle at 50% 82%, transparent 0, transparent 4.5mm, rgba(29,53,87,.012) 4.5mm, rgba(29,53,87,.012) 5mm);
        }

        /* ── Security Layer 2: Diagonal UV watermark text ── */
        .sec-uv {
            position: absolute; top: 13mm; left: 13mm; right: 13mm; bottom: 13mm;
            overflow: hidden; opacity: 0.038; z-index: 1; pointer-events: none;
            font-family: "DejaVu Sans", Arial, sans-serif; font-size: 7pt;
            color: #1d3557; white-space: nowrap;
        }
        .sec-uv-line {
            position: absolute; left: -20mm; right: -20mm;
            transform-origin: left center;
            transform: rotate(-18deg);
        }

        /* ── Security Layer 3: Micro-text strip ── */
        .sec-micro {
            position: absolute; top: 110mm; left: 14mm; right: 14mm;
            height: 4mm; overflow: hidden; opacity: 0.06; z-index: 1;
            pointer-events: none; font-family: "DejaVu Sans Mono", monospace;
            font-size: 2.8pt; color: #b8232b; letter-spacing: .4pt;
            line-height: 1.5; word-break: break-all;
        }

        /* ── Borders ── */
        .border-outer {
            position: absolute; top: 7mm; left: 7mm; right: 7mm; bottom: 7mm;
            border: 1.2mm solid #b8232b; z-index: 2;
        }
        .border-mid {
            position: absolute; top: 9.8mm; left: 9.8mm; right: 9.8mm; bottom: 9.8mm;
            border: .25mm solid #b8232b; z-index: 2;
        }
        .border-inner {
            position: absolute; top: 12.5mm; left: 12.5mm; right: 12.5mm; bottom: 12.5mm;
            border: .7mm double #1d3557; z-index: 2;
        }

        /* ── Corner rosettes ── */
        .corner { position: absolute; width: 11mm; height: 11mm; z-index: 3; }
        .corner-tl { top: 13mm;  left: 13mm; }
        .corner-tr { top: 13mm;  right: 13mm; transform: scaleX(-1); }
        .corner-bl { bottom: 13mm; left: 13mm;  transform: scaleY(-1); }
        .corner-br { bottom: 13mm; right: 13mm;  transform: scale(-1,-1); }

        /* ── Main Content Block (Fluid inner layout) ── */
        .cert-content {
            position: absolute;
            top: 16mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            z-index: 4;
        }

        .logo { margin-bottom: 3mm; line-height: 1; }
        .logo img { width: 50mm; height: auto; display: block; margin: 0 auto; }

        .title {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 20pt; font-weight: bold; color: #1d3557;
            letter-spacing: 3px; text-transform: uppercase;
            line-height: 1.2; margin-bottom: 1mm;
        }
        .title-sub {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 7.5pt; color: #666;
            letter-spacing: 2px; text-transform: uppercase;
            line-height: 1.2; margin-bottom: 5mm;
        }

        .certify {
            font-size: 9pt; color: #555; font-style: italic;
            line-height: 1.3; margin-bottom: 4mm;
        }

        .recipient {
            font-size: 24pt; font-style: italic; color: #1a1a1a;
            line-height: 1.2; margin-bottom: 4mm;
            border-bottom: 0.25mm solid #ccc;
            display: inline-block; padding-bottom: 1mm; padding-left: 15mm; padding-right: 15mm;
        }

        .for-completing {
            font-size: 9pt; color: #555; font-style: italic;
            line-height: 1.3; margin-bottom: 2mm;
        }
        .course-name {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 14pt; font-weight: bold; color: #1d3557;
            line-height: 1.3; margin-bottom: 5mm;
        }

        .meta { margin-bottom: 5mm; line-height: 1; }
        .pill {
            display: inline-block; background: #f4f6fa; border: .25mm solid #dde2ec;
            border-radius: 1mm; padding: 1mm 3mm;
            font-family: "DejaVu Sans", Arial, sans-serif; font-size: 7.5pt; color: #444; margin: 0 1mm;
        }
        .pill strong { color: #1d3557; }

        .dates { margin-bottom: 0px; line-height: 1; }
        .date-cell { display: inline-block; text-align: center; padding: 0 10mm; }
        .date-cell + .date-cell { border-left: .3mm solid #ddd; }
        .date-lbl {
            display: block; font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 6.5pt; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 1.5mm;
        }
        .date-val { font-size: 9.5pt; font-weight: bold; color: #1a1a1a; }
        .date-expired { color: #b8232b; }


        /* ── Absolute Anchored Footer Area (Locks onto Page 1) ── */
        .footer-zone {
            position: absolute;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            height: 30mm;
            z-index: 5;
        }

        .sig-wrap {
            position: absolute;
            bottom: 4mm;
            left: 0;
            width: 160mm;
        }
        .sig-block {
            display: inline-block;
            width: 46mm;
            text-align: center;
            margin-right: 6mm;
            vertical-align: bottom;
        }
        .sig-rule { border-top: .35mm solid #aaa; padding-top: 2mm; }
        .sig-name { font-size: 8pt; font-weight: bold; color: #222; }
        .sig-title {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 6.5pt; color: #999; margin-top: 0.5mm;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        .qr-wrap {
            position: absolute;
            bottom: 4mm;
            right: 0;
            width: 30mm;
            text-align: center;
        }
        .qr-wrap img {
            width: 24mm; height: 24mm;
            display: block; margin: 0 auto;
            border: .25mm solid #dcdcdc; padding: 0.5mm; background: #fff;
        }
        .qr-lbl {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 5.5pt; color: #999; margin-top: 1mm;
            text-transform: uppercase; letter-spacing: 0.5px;
        }

        .cert-strip {
            position: absolute;
            bottom: 0mm;
            left: -5mm;
            right: -5mm;
            text-align: center;
            font-family: "DejaVu Sans Mono", monospace;
            font-size: 6pt; color: #bbb;
            border-top: .15mm solid #eaeaea; padding-top: 1.5mm;
        }
        .cert-strip code { color: #666; }
    </style>
</head>
<body>

<div class="sec-bg"></div>

@php
    $uvLine = 'ER MEDICS · AUTHENTIC · ' . $certificate->certificate_number . ' · ' . $certificate->verification_code . ' · ';
    $uvLine = str_repeat($uvLine, 5);
@endphp
<div class="sec-uv">
    @for ($i = 0; $i < 13; $i++)
        <div class="sec-uv-line" style="top: {{ ($i * 14) + 2 }}mm;">{{ $uvLine }}</div>
    @endfor
</div>

@php
    $micro = 'ERMEDICS·' . $certificate->certificate_number . '·' . $certificate->verification_code . '·VERIFIED·';
    $micro = str_repeat($micro, 35);
@endphp
<div class="sec-micro">{{ $micro }}</div>

<div class="border-outer"></div>
<div class="border-mid"></div>
<div class="border-inner"></div>

@php
    $svg = '<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">'
         . '<g fill="none" stroke="#b8232b" stroke-width="3">'
         . '<path d="M8,8 L8,45 Q8,8 45,8"/>'
         . '<path d="M8,8 L8,30 Q8,8 30,8"/>'
         . '<path d="M8,8 L8,18 Q8,8 18,8"/>'
         . '</g>'
         . '<circle cx="8" cy="8" r="3.5" fill="#b8232b" opacity=".55"/>'
         . '<circle cx="8" cy="8" r="1.5" fill="#fff"/>'
         . '</svg>';
@endphp
<div class="corner corner-tl">{!! $svg !!}</div>
<div class="corner corner-tr">{!! $svg !!}</div>
<div class="corner corner-bl">{!! $svg !!}</div>
<div class="corner corner-br">{!! $svg !!}</div>

<div class="cert-content">
    <div class="logo">
        <img src="{{ public_path('logo.jpeg') }}" alt="ER Medics">
    </div>

    <div class="title">Certificate of Completion</div>
    <div class="title-sub">Emergency Medical Training &amp; SHEQ</div>

    <div class="certify">
        This is to certify that the following individual has successfully fulfilled all requirements
    </div>

    <div class="recipient">{{ $certificate->student?->full_name }}</div>

    <div class="for-completing">has successfully completed the course</div>
    <div class="course-name">{{ $certificate->course?->title }}</div>

    <div class="meta">
        <span class="pill">Code&nbsp;<strong>{{ $certificate->course?->code }}</strong></span>
        <span class="pill">Duration&nbsp;<strong>{{ $certificate->course?->duration_hours }}&nbsp;hrs</strong></span>
        @if ($certificate->score !== null)
            <span class="pill">Score&nbsp;<strong>{{ number_format($certificate->score, 1) }}%</strong></span>
        @endif
        @if ($certificate->grade)
            <span class="pill">Grade&nbsp;<strong>{{ $certificate->grade }}</strong></span>
        @endif
    </div>

    <div class="dates">
        <span class="date-cell">
            <span class="date-lbl">Date of Issue</span>
            <span class="date-val">{{ optional($certificate->issued_at)->format('d F Y') ?? '—' }}</span>
        </span>
        @if ($certificate->expires_at)
        <span class="date-cell">
            <span class="date-lbl">Expiry Date</span>
            <span class="date-val {{ $certificate->isExpired() ? 'date-expired' : '' }}">{{ $certificate->expires_at->format('d F Y') }}</span>
        </span>
        @endif
    </div>
</div>

<div class="footer-zone">
    <div class="sig-wrap">
        <div class="sig-block">
            <div class="sig-rule">
                <div class="sig-name">{{ \Illuminate\Support\Str::limit($certificate->issuer?->name ?? 'Authorised Signatory', 22) }}</div>
                <div class="sig-title">Issuing Officer</div>
            </div>
        </div>
        <div class="sig-block">
            <div class="sig-rule">
                <div class="sig-name">ER Medics</div>
                <div class="sig-title">Organisation</div>
            </div>
        </div>
        <div class="sig-block">
            <div class="sig-rule">
                <div class="sig-name">{{ optional($certificate->issued_at)->format('d F Y') }}</div>
                <div class="sig-title">Date Issued</div>
            </div>
        </div>

        Code:&nbsp;<code>{{ $certificate->verification_code }}</code>
    </div>

    <div class="qr-wrap">
        @php
            $qrSvg = base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(150)
                    ->margin(1)
                    ->generate($verifyUrl)
            );
        @endphp
        <img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="Verify">
        <div class="qr-lbl">Scan to verify</div>
    </div>

    <div class="cert-strip">
        Cert:&nbsp;<code>{{ $certificate->certificate_number }}</code>
        &nbsp;&bull;&nbsp;
        Code:&nbsp;<code>{{ $certificate->verification_code }}</code>
        &nbsp;&bull;&nbsp;
        {{ $verifyUrl }}
    </div>
</div>

</body>
</html>
