<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 10pt;
    color: #1a1a2e;
    background: #ffffff;
    padding: 0;
  }

  /* ── Page border ── */
  .page {
    width: 100%;
    min-height: 267mm;
    padding: 10mm 14mm 10mm 14mm;
    border: 3px solid #c0392b;
    position: relative;
  }
  .page-inner {
    border: 1px solid #c0392b;
    padding: 8mm 10mm;
    min-height: 245mm;
  }

  /* ── Header ── */
  .header {
    border-bottom: 2px solid #c0392b;
    padding-bottom: 6mm;
    margin-bottom: 6mm;
    display: table;
    width: 100%;
  }
  .header-logo { display: table-cell; width: 50%; vertical-align: middle; }
  .header-logo img { width: 120px; height: auto; }
  .header-title { display: table-cell; text-align: right; vertical-align: middle; }
  .header-title .receipt-label {
    font-size: 20pt;
    font-weight: bold;
    color: #c0392b;
    text-transform: uppercase;
    letter-spacing: 2px;
  }
  .header-title .receipt-number {
    font-size: 10pt;
    color: #666;
    margin-top: 2px;
  }

  /* ── Meta strip ── */
  .meta-strip {
    background: #f7f7f7;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 4mm 6mm;
    margin-bottom: 6mm;
    display: table;
    width: 100%;
  }
  .meta-cell { display: table-cell; width: 33%; vertical-align: top; }
  .meta-label { font-size: 7pt; color: #888; text-transform: uppercase; letter-spacing: .5px; }
  .meta-value { font-size: 9.5pt; font-weight: bold; color: #1a1a2e; margin-top: 1px; }

  /* ── Section headings ── */
  .section-head {
    background: #1a1a2e;
    color: #fff;
    font-size: 8pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 3px 8px;
    margin-bottom: 0;
  }

  /* ── Customer & Booking info tables ── */
  .info-table { width: 100%; border-collapse: collapse; margin-bottom: 6mm; }
  .info-table td { padding: 3px 6px; font-size: 9.5pt; border-bottom: 1px solid #f0f0f0; }
  .info-table td.lbl { color: #888; width: 35%; font-size: 8.5pt; }
  .info-table td.val { font-weight: 500; }

  /* ── Items table ── */
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 6mm; }
  .items-table thead tr { background: #1a1a2e; color: #fff; }
  .items-table thead th {
    padding: 5px 8px;
    font-size: 8pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: .5px;
    text-align: left;
  }
  .items-table thead th.r { text-align: right; }
  .items-table tbody tr:nth-child(even) { background: #fafafa; }
  .items-table tbody td { padding: 5px 8px; font-size: 9pt; border-bottom: 1px solid #ebebeb; }
  .items-table tbody td.r { text-align: right; }
  .items-table tfoot tr { border-top: 2px solid #1a1a2e; }
  .items-table tfoot td { padding: 6px 8px; font-size: 10pt; font-weight: bold; }
  .items-table tfoot td.r { text-align: right; color: #c0392b; font-size: 11pt; }

  /* ── Payment info ── */
  .payment-row {
    display: table;
    width: 100%;
    margin-bottom: 6mm;
  }
  .payment-cell { display: table-cell; width: 50%; vertical-align: top; }
  .payment-box {
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    padding: 4mm;
    margin-right: 4mm;
  }
  .payment-box .paid-stamp {
    font-size: 18pt;
    font-weight: bold;
    color: #27ae60;
    text-transform: uppercase;
    letter-spacing: 3px;
    border: 3px solid #27ae60;
    display: inline-block;
    padding: 2px 10px;
    border-radius: 4px;
    opacity: .85;
    transform: rotate(-8deg);
  }

  /* ── Footer ── */
  .footer {
    border-top: 1px solid #e0e0e0;
    margin-top: 6mm;
    padding-top: 4mm;
    text-align: center;
    font-size: 7.5pt;
    color: #aaa;
  }
  .footer strong { color: #c0392b; }
</style>
</head>
<body>
<div class="page">
<div class="page-inner">

  {{-- ── HEADER ── --}}
  <div class="header">
    <div class="header-logo">
      <img src="{{ public_path('logo.jpeg') }}" alt="ER Medics">
    </div>
    <div class="header-title">
      <div class="receipt-label">Receipt</div>
      <div class="receipt-number">{{ $receipt->receipt_number }}</div>
    </div>
  </div>

  {{-- ── META STRIP ── --}}
  <div class="meta-strip">
    <div class="meta-cell">
      <div class="meta-label">Date issued</div>
      <div class="meta-value">{{ $receipt->created_at->format('d F Y') }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Booking reference</div>
      <div class="meta-value">{{ $receipt->booking->booking_code }}</div>
    </div>
    <div class="meta-cell">
      <div class="meta-label">Amount paid</div>
      <div class="meta-value" style="color:#c0392b; font-size:12pt;">
        {{ format_money($receipt->amount) }}
      </div>
    </div>
  </div>

  {{-- ── CUSTOMER ── --}}
  <div class="section-head">Billed to</div>
  <table class="info-table">
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
      <td class="val" colspan="3">{{ strtoupper($receipt->booking->id_type) }} · {{ $receipt->booking->id_number }}</td>
    </tr>
    @endif
  </table>

  {{-- ── ITEMS ── --}}
  <div class="section-head">Courses</div>
  <table class="items-table">
    <thead>
      <tr>
        <th>Course</th>
        <th>Code</th>
        <th>Schedule</th>
        <th class="r">Qty</th>
        <th class="r">Unit price</th>
        <th class="r">Subtotal</th>
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
                <br><span style="font-size:8pt;color:#888;">{{ $item->schedule->location->name }}</span>
              @endif
            @else
              <span style="color:#aaa;">TBD</span>
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
        <td colspan="5" style="text-align:right;">Total</td>
        <td class="r">{{ format_money($receipt->amount) }}</td>
      </tr>
    </tfoot>
  </table>

  {{-- ── PAYMENT & STAMP ── --}}
  <div class="payment-row">
    <div class="payment-cell">
      <div class="payment-box">
        <div class="meta-label" style="margin-bottom:4px;">Payment method</div>
        <div style="font-weight:bold; font-size:10pt;">
          {{ $receipt->payment->paymentChannel?->name ?? 'Unknown' }}
        </div>
        @if ($receipt->payment->uuid)
          <div style="font-size:7.5pt; color:#aaa; margin-top:3px; font-family:monospace;">
            Ref: {{ $receipt->payment->uuid }}
          </div>
        @endif
        <div style="font-size:8.5pt; color:#888; margin-top:4px;">
          {{ $receipt->payment->updated_at?->format('d M Y, H:i') }}
        </div>
      </div>
    </div>
    <div class="payment-cell" style="text-align:center; padding-top:6px;">
      <div class="paid-stamp">PAID</div>
    </div>
  </div>

  {{-- ── FOOTER ── --}}
  <div class="footer">
    <strong>ER Medics</strong> &nbsp;|&nbsp;
    This is an official receipt. Please retain for your records.
    &nbsp;|&nbsp; Generated {{ now()->format('d M Y H:i') }}
  </div>

</div>
</div>
</body>
</html>
