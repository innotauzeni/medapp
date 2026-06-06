<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->certificate_number }}</title>
    <style>
        @page { margin: 0; size: A4 portrait; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            width: 210mm; height: 297mm; overflow: hidden; background: #fff;
            font-family: "DejaVu Serif", Georgia, serif; color: #1d3557; position: relative;
        }

        /* ── Decorative navy frame ── */
        .frame-outer { position: absolute; top: 8mm;  left: 8mm;  right: 8mm;  bottom: 8mm;  border: 1.4mm solid #1d3557; z-index: 1; }
        .frame-mid   { position: absolute; top: 10mm; left: 10mm; right: 10mm; bottom: 10mm; border: .3mm solid #1d3557;  z-index: 1; }
        .frame-inner { position: absolute; top: 12mm; left: 12mm; right: 12mm; bottom: 12mm; border: .6mm solid #1d3557;  z-index: 1; }

        /* ── Top content ── */
        .content {
            position: absolute; top: 20mm; left: 20mm; right: 20mm;
            text-align: center; z-index: 3;
        }
        .logo img { height: 26mm; width: auto; }

        .title {
            font-size: 50pt; font-weight: bold; color: #1d3557;
            line-height: 1.05; margin-top: 1mm; letter-spacing: .5px;
        }
        .divider img { width: 72mm; height: auto; margin: 1mm 0 4mm; }

        .presented {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 9pt; color: #b8232b; letter-spacing: 3px;
            text-transform: uppercase; margin-bottom: 9mm;
        }

        .recipient {
            font-size: 30pt; font-weight: bold; color: #1d3557; line-height: 1.1;
            display: inline-block; padding: 0 14mm 2mm; border-bottom: .6mm solid #1d3557;
        }

        .attended {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 12pt; color: #444; margin: 8mm 0 3mm;
        }
        .course-name {
            font-family: "DejaVu Sans", Arial, sans-serif;
            font-size: 15pt; font-weight: bold; color: #1d3557;
            line-height: 1.35; margin: 0 6mm;
        }

        /* ── Dates (centre) ── */
        .dates { position: absolute; top: 179mm; left: 0; right: 0; text-align: center; z-index: 3; }
        .date-val {
            font-family: "DejaVu Sans", Arial, sans-serif; font-weight: bold;
            font-size: 12pt; color: #1d3557; display: inline-block;
            padding: 0 8mm 1mm; border-bottom: .4mm solid #1d3557;
        }
        .date-lbl {
            font-family: "DejaVu Serif", Georgia, serif;
            font-size: 11pt; color: #333; margin: 1.5mm 0 5mm;
        }

        /* ── Signatures ── */
        .signs { position: absolute; bottom: 46mm; left: 22mm; right: 22mm; z-index: 3; }
        .signs table { width: 100%; border-collapse: collapse; }
        .signs td { vertical-align: bottom; text-align: center; }
        .signs .side { width: 38%; }
        .signs .mid  { width: 24%; }
        .sig-line { border-top: .4mm solid #1d3557; width: 46mm; margin: 0 auto; padding-top: 2mm; }
        .sig-role {
            font-family: "DejaVu Serif", Georgia, serif;
            font-weight: bold; font-size: 11pt; color: #1d3557;
        }

        /* ── Footer marks ── */
        .star img  { width: 26mm; height: auto; }
        .star      { position: absolute; bottom: 16mm; left: 50%; margin-left: -13mm; z-index: 3; }
        .qr        { position: absolute; bottom: 14mm; left: 20mm; z-index: 3; text-align: center; }
        .qr img    { width: 22mm; height: 22mm; }
        .qr-lbl    { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 5.5pt; color: #888; margin-top: 1mm; text-transform: uppercase; letter-spacing: .5px; }
        .form-label{
            position: absolute; bottom: 22mm; right: 24mm; z-index: 3; font-family: "DejaVu Sans", Arial, sans-serif; font-size: 5.5pt; color: #888; margin-top: 1mm; text-transform: uppercase; letter-spacing: .5px;
        }
        .cert-no   { position: absolute; bottom: 16mm; right: 22mm; z-index: 3; font-family: "DejaVu Sans", Arial, sans-serif; font-weight: bold; font-size: 10pt; color: #b8232b; letter-spacing: 1px; }
    </style>
</head>
<body>

<div class="frame-outer"></div>
<div class="frame-mid"></div>
<div class="frame-inner"></div>

<div class="content">
    <div class="logo"><img src="{{ public_path('logo.jpeg') }}" alt="ER Medics"></div>

    <div class="title">Certificate</div>

    <div class="divider"><img src="{{ public_path('flower.png') }}" alt=""></div>

    <div class="presented">This Certificate is Proudly Presented To</div>

    <div class="recipient">{{ strtoupper($certificate->student?->full_name) }}</div>

    <div class="attended">Has attended the course in</div>
    <div class="course-name">{{ strtoupper($certificate->course?->title) }}</div>
</div>

<div class="dates">
    <div class="date-val">{{ optional($certificate->issued_at)->format('d F Y') ? strtoupper($certificate->issued_at->format('d F Y')) : '—' }}</div>
    <div class="date-lbl">Date issued</div>
    <div class="date-val">{{ $certificate->expires_at ? strtoupper($certificate->expires_at->format('d F Y')) : '—' }}</div>
    <div class="date-lbl">Date of Expiry</div>
</div>

<div class="signs">
    <table>
        <tr>
            <td class="side">
                <div class="sig-line"><div class="sig-role">Medical Director</div></div>
            </td>
            <td class="mid">&nbsp;</td>
            <td class="side">
                <div class="sig-line"><div class="sig-role">Vice President</div></div>
            </td>
        </tr>
    </table>
</div>

<div class="star"><img src="{{ public_path('footimage.png') }}" alt="Star of Life"></div>

<div class="qr">
    @php
        $qrSvg = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(1)->generate($verifyUrl)
        );
    @endphp
    <img src="data:image/svg+xml;base64,{{ $qrSvg }}" alt="Verify">
    <div class="qr-lbl">Scan to verify</div>
</div>
<b class="form-label">Verify Certificate Number</b><br>
<div class="cert-no">

    {{ $certificate->display_number }}
</div>

</body>
</html>
