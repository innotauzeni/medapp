<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 9.5pt;
    color: #1a1a2e;
    background: #ffffff;
    /* Equal 14mm margins on all four sides — DomPDF respects body padding.
       NOTE: no width:100% here. DomPDF doesn't reliably support
       box-sizing:border-box, so a block element with both an explicit
       width AND padding/border will overflow (padding gets added on
       top instead of being included). Block elements fill their
       container by default with no width declared, so we just omit it. */
    padding: 14mm;
  }

  /* ─────────────────────────────────────────
     Single outer border — no inner border
  ───────────────────────────────────────── */
  .page {
    border: 2.5px solid #c0392b;
    padding: 8mm;
    /* same fix: no width:100% combined with border+padding */
  }

  /* ─────────────────────────────────────────
     Clearfix helper
  ───────────────────────────────────────── */
  .cf:after { content: ''; display: table; clear: both; }

  /* ─────────────────────────────────────────
     HEADER  —  logo left, title right
     Uses explicit float widths (DomPDF safe)
  ───────────────────────────────────────── */
  .hdr {
    border-bottom: 2px solid #c0392b;
    padding-bottom: 5mm;
    margin-bottom: 5mm;
  }
  .hdr-logo {
    float: left;
    width: 45%;
  }
  .hdr-logo img {
    width: 110px;
    height: auto;
  }
  .hdr-title {
    float: right;
    width: 55%;
    text-align: right;
  }
  .hdr-title .lbl-receipt {
    font-size: 22pt;
    font-weight: bold;
    color: #c0392b;
    text-transform: uppercase;
    letter-spacing: 3px;
    line-height: 1;
  }
  .hdr-title .lbl-number {
    font-size: 9.5pt;
    color: #777;
    margin-top: 3px;
  }

  /* ─────────────────────────────────────────
     META STRIP — now a table instead of
     floated/padded divs. Table cells are
     special-cased in the box model: padding
     is included in the cell's width even
     under content-box, so this is safe in
     DomPDF without relying on box-sizing.
  ───────────────────────────────────────── */
  .meta-tbl {
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    border-collapse: collapse;
    margin-bottom: 5mm;
  }
  .meta-tbl td {
    width: 33.33%;
    padding: 4mm 5mm;
    vertical-align: top;
  }
  .meta-tbl td.divide {
    border-left: 1px solid #e0e0e0;
  }
  .ml  { font-size: 7pt; color: #999; text-transform: uppercase; letter-spacing: .5px; }
  .mv  { font-size: 10pt; font-weight: bold; color: #1a1a2e; margin-top: 2px; }
  .mv-red { color: #c0392b; font-size: 11pt; }

  /* ─────────────────────────────────────────
     SECTION HEADING
  ───────────────────────────────────────── */
  .sh {
    background: #1a1a2e;
    color: #fff;
    font-size: 7.5pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 3px 6px;
    margin-bottom: 0;
  }

  /* ─────────────────────────────────────────
     INFO TABLE (billed to)
  ───────────────────────────────────────── */
  .info-tbl {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 5mm;
    font-size: 9pt;
  }
  .info-tbl td {
    padding: 3px 6px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: top;
  }
  .info-tbl .lbl { color: #999; width: 18%; font-size: 8pt; white-space: nowrap; }
  .info-tbl .val { font-weight: 500; width: 32%; }

  /* ─────────────────────────────────────────
     ITEMS TABLE
  ───────────────────────────────────────── */
  .items-tbl {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 5mm;
    font-size: 9pt;
  }
  .items-tbl thead tr { background: #1a1a2e; color: #fff; }
  .items-tbl thead th {
    padding: 4px 6px;
    font-size: 7.5pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .4px;
    text-align: left;
  }
  .items-tbl thead th.r { text-align: right; }
  .items-tbl tbody tr:nth-child(even) { background: #fafafa; }
  .items-tbl tbody td {
    padding: 4px 6px;
    border-bottom: 1px solid #ececec;
    vertical-align: top;
  }
  .items-tbl tbody td.r { text-align: right; }
  .items-tbl tfoot td {
    padding: 5px 6px;
    font-weight: bold;
    font-size: 10pt;
    border-top: 2px solid #1a1a2e;
  }
  .items-tbl tfoot td.r { text-align: right; color: #c0392b; }

  /* ─────────────────────────────────────────
     PAYMENT ROW — float left/right
     (no padding/border on the floated wrappers
     themselves, so width % here is safe)
  ───────────────────────────────────────── */
  .pay-left {
    float: left;
    width: 55%;
    margin-bottom: 5mm;
  }
  .pay-right {
    float: right;
    width: 40%;
    text-align: center;
    padding-top: 4mm;
    margin-bottom: 5mm;
  }
  .pay-box {
    border: 1px solid #e0e0e0;
    padding: 4mm;
    font-size: 9pt;
    /* no width declared — fills .pay-left's content width automatically */
  }
  .paid-stamp {
    font-size: 22pt;
    font-weight: bold;
    color: #16a34a;
    text-transform: uppercase;
    letter-spacing: 4px;
    border: 3px solid #16a34a;
    display: inline-block;
    padding: 3px 14px;
  }

  /* ─────────────────────────────────────────
     FOOTER
  ───────────────────────────────────────── */
  .footer {
    border-top: 1px solid #e0e0e0;
    padding-top: 4mm;
    text-align: center;
    font-size: 7.5pt;
    color: #aaa;
    clear: both;
  }
  .footer strong { color: #c0392b; }
</style>
</head>
<body>
<div class="page">

  {{-- ── HEADER ── --}}
  <div class="hdr cf">
    <div class="hdr-logo">
      <img src="{{ public_path('logo.jpeg') }}" alt="ER Medics">
    </div>
    <div class="hdr-title">
      <div class="lbl-receipt">Receipt</div>
      <div class="lbl-number">{{ $receipt->receipt_number }}</div>
    </div>
  </div>

  {{-- ── META STRIP ── --}}
  <table class="meta-tbl">
    <tr>
      <td>
        <div class="ml">Date issued</div>
        <div class="mv">{{ $receipt->created_at->format('d F Y') }}</div>
      </td>
      <td class="divide">
        <div class="ml">Booking reference</div>
        <div class="mv">{{ $receipt->booking->booking_code }}</div>
      </td>
      <td class="divide">
        <div class="ml">Amount paid</div>
        <div class="mv mv-red">{{ format_money($receipt->amount) }}</div>
      </td>
    </tr>
  </table>

  {{-- ── BILLED TO ── --}}
  <div class="sh">Billed to</div>
  <table class="info-tbl">
    <tr>
      <td class="lbl">Full name</td>
      <td class="val">{{ $receipt->booking->full_name }}</td>
      <td class="lbl">Email</td>
      <td class="val">{{ $receipt->booking->email }}</td>
    </tr>
    <tr>
      <td class="lbl">Phone</td>
      <td class="val">{{ $receipt->booking->phone }}</td>
      <td class="lbl">Organisation</td>
      <td class="val">{{ $receipt->booking->organization ?: '—' }}</td>
    </tr>
    @if ($receipt->booking->id_number)
    <tr>
      <td class="lbl">ID / Passport</td>
      <td class="val" colspan="3">
        {{ strtoupper($receipt->booking->id_type) }} &middot; {{ $receipt->booking->id_number }}
      </td>
    </tr>
    @endif
  </table>

  {{-- ── COURSES ── --}}
  <div class="sh">Courses</div>
  <table class="items-tbl">
    <thead>
      <tr>
        <th style="width:34%;">Course</th>
        <th style="width:10%;">Code</th>
        <th style="width:20%;">Schedule</th>
        <th class="r" style="width:6%;">Qty</th>
        <th class="r" style="width:15%;">Unit price</th>
        <th class="r" style="width:15%;">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($receipt->booking->items as $item)
        <tr>
          <td>{{ $item->course?->title ?? '—' }}</td>
          <td>{{ $item->course?->code ?? '—' }}</td>
          <td>
            @if ($item->schedule)
              {{ $item->schedule->start_date->format('d M Y') }}
              @if ($item->schedule->location)
                <br><span style="font-size:7.5pt;color:#999;">{{ $item->schedule->location->name }}</span>
              @endif
            @else
              <span style="color:#bbb;">TBD</span>
            @endif
          </td>
          <td class="r">{{ $item->quantity }}</td>
          <td class="r">{{ format_money($item->unit_price) }}</td>
          <td class="r">{{ format_money($item->unit_price * $item->quantity) }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5" style="text-align:right; color:#555;">Total</td>
        <td class="r">{{ format_money($receipt->amount) }}</td>
      </tr>
    </tfoot>
  </table>

  {{-- ── PAYMENT & PAID STAMP ── --}}
  <div class="cf">
    <div class="pay-left">
      <div class="pay-box">
        <div class="ml" style="margin-bottom:3px;">Payment method</div>
        <div style="font-weight:bold; font-size:10pt;">
          {{ $receipt->payment->paymentChannel?->name ?? 'Unknown' }}
        </div>
        @if ($receipt->payment->uuid)
          <div style="font-size:7pt; color:#bbb; margin-top:3px; font-family:monospace; word-break:break-all;">
            Ref: {{ $receipt->payment->uuid }}
          </div>
        @endif
        <div style="font-size:8pt; color:#999; margin-top:4px;">
          {{ $receipt->payment->updated_at?->format('d M Y, H:i') }}
        </div>
      </div>
    </div>
    <div class="pay-right">
      <div class="paid-stamp">PAID</div>
    </div>
  </div>

  {{-- ── FOOTER ── --}}
  <div class="footer">
    <strong>ER Medics</strong>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    This is an official receipt. Please retain for your records.
    &nbsp;&nbsp;|&nbsp;&nbsp;
    Generated {{ now()->format('d M Y H:i') }}
  </div>

</div>
</body>
</html>