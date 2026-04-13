@php
$settings_data = \App\Models\Utility::settingsById($invoice->created_by);

// Get company logo
$company_logo = null;
if (isset($settings_data['company_logo_dark']) && !empty($settings_data['company_logo_dark'])) {
    $company_logo = asset('storage/uploads/logo/' . $settings_data['company_logo_dark']);
} elseif (isset($settings['company_logo_dark']) && !empty($settings['company_logo_dark'])) {
    $company_logo = asset('storage/uploads/logo/' . $settings['company_logo_dark']);
}

// Get driving license information for this customer
$license = null;
if (!empty($customer)) {
    $license = \App\Models\DrivingLicense::where('customer_id', $customer->id)
        ->where('invoice_id', $invoice->id)
        ->first();

    // If no license found for this invoice, get the latest license for the customer
    if (!$license) {
        $license = \App\Models\DrivingLicense::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}

// Get airline/vendor information
$airline = null;
if (!empty($invoice->vender_id)) {
    $airline = \App\Models\Vender::find($invoice->vender_id);
}

// Resolve human-readable Origin / Destination.
// If stored as numeric ID, try to map to ProductService name; otherwise use raw text.
$originName = null;
if (!empty($invoice->origin)) {
    if (is_numeric($invoice->origin)) {
        $originName = optional(\App\Models\ProductService::find($invoice->origin))->name ?? $invoice->origin;
    } else {
        $originName = $invoice->origin;
    }
}

$destinationName = null;
if (!empty($invoice->destination)) {
    if (is_numeric($invoice->destination)) {
        $destinationName = optional(\App\Models\ProductService::find($invoice->destination))->name ?? $invoice->destination;
    } else {
        $destinationName = $invoice->destination;
    }
}
@endphp


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=2.0">
<title>Kalsan Driving Certificate</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    background: #d0d0d0;
    display: flex;
    flex-direction: column;  /* এটা যোগ করুন */
    justify-content: flex-start;
    align-items: center;     /* center করুন */
    min-height: 100vh;
    padding: 10px;
    font-family: Georgia, 'Times New Roman', serif;
}

  .page {
    width: 210mm;
    min-height: 300mm;
    background: #ffffff;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .border-top, .border-bottom {
    position: absolute; left: 0; right: 0; height: 20px;
    background: repeating-linear-gradient(90deg, #1a6b9a 0px, #1a6b9a 9px, #5ab0d8 9px, #5ab0d8 18px);
    z-index: 10;
  }
  .border-top { top: 0; }
  .border-bottom { bottom: 0; }

  .border-left, .border-right {
    position: absolute; top: 0; bottom: 0; width: 20px;
    background: repeating-linear-gradient(180deg, #1a6b9a 0px, #1a6b9a 9px, #5ab0d8 9px, #5ab0d8 18px);
    z-index: 10;
  }
  .border-left { left: 0; }
  .border-right { right: 0; }

  .inner-border {
    position: absolute;
    top: 26px; left: 26px; right: 26px; bottom: 26px;
    border: 1.5px solid #1a6b9a;
    z-index: 5;
    pointer-events: none;
  }

  .corner { position: absolute; width: 44px; height: 44px; z-index: 12; }
  .corner-tl { top: 4px; left: 4px; }
  .corner-tr { top: 4px; right: 4px; }
  .corner-bl { bottom: 4px; left: 4px; }
  .corner-br { bottom: 4px; right: 4px; }

  /* Main flex layout */
  .content {
    flex: 1;
    position: relative;
    z-index: 6;
    padding: 20px 40px 20px 40px;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  /* Header */
  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 7px;
    flex-shrink: 0;
  }
  .logo-name {
    font-family: Arial, sans-serif;
    font-weight: 900;
    font-size: 28px;
    color: #1a6b9a;
    line-height: 1;
    letter-spacing: 1px;
  }
  .logo-sub {
    font-family: Arial, sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: #1a6b9a;
    line-height: 1.35;
  }
  .center-logo {
    width: 70px; height: 70px;
    border: 2.5px solid #1a6b9a;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
  }
  .center-logo-text {
    font-family: Arial, sans-serif;
    font-size: 6px;
    font-weight: 700;
    color: #1a6b9a;
    text-align: center;
    line-height: 1.3;
  }
  .arabic-main {
    font-family: Arial, sans-serif;
    font-size: 26px;
    font-weight: 700;
    color: #1a6b9a;
    direction: rtl;
    line-height: 1.1;
    text-align: right;
  }
  .arabic-sub {
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #1a6b9a;
    direction: rtl;
    text-align: right;
  }

  /* Date */
  .date-row {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 4px;
    flex-shrink: 0;
  }
  .date-box {
    border: 1px solid #666;
    padding: 3px 12px;
    font-family: Arial, sans-serif;
    font-size: 11px;
    color: #333;
  }

  /* CENTER — fills remaining vertical space */
  .center-block {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: 0;  /* important for flex children to prevent overflow */
    gap: 0;
  }

  .cert-title {
    font-family: Arial, sans-serif;
    font-size: 40px;
    font-weight: 700;
    color: #1a9ad7;
    line-height: 1;
    margin-bottom: 5px;
  }
  .cert-subtitle {
    font-family: Arial, sans-serif;
    font-size: 16px;
    font-style: italic;
    font-weight: 700;
    color: #1a6b9a;
    text-decoration: underline;
    margin-bottom: 16px;
  }
  .photo-box {
    border: 2px solid #1a9ad7;
    width: 130px;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eaf5fb;
    margin-bottom: 8px;
    overflow: hidden;
  }
  .user-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 2px;
  }
  .photo-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
  }
  .company-logo {
    max-height: 100px;
    max-width: 220px;
    object-fit: contain;
  }
  .grade-box {
    border: 1.5px solid #1a9ad7;
    padding: 3px 52px;
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #333;
    margin-bottom: 14px;
  }
  .holder-name {
    font-family: Arial, sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #1a6b9a;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
  }
  .license-no {
    font-family: Arial, sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #222;
    margin-bottom: 8px;
  }
  .license-no span { color: #cc2200; }
  .serial {
    font-family: Arial, sans-serif;
    font-size: 15px;
    font-weight: 700;
    color: #222;
    margin-bottom: 16px;
  }
  .serial span { color: #cc2200; }
  .cert-body {
    font-style: italic;
    font-size: 14px;
    color: #222;
    line-height: 2;
  }
  .cert-body .ul { text-decoration: underline; }

  /* SIGNATURE SECTION */
  .signature-section {
    flex-shrink: 0;
    border-top: 1.5px dashed #bbb;
    padding-top: 16px;
    padding-bottom: 4px;
    display: flex;
    margin-top: auto;
    align-items: flex-end;
    justify-content: space-between;
    align-items: flex-start;
  }

  .sig-block {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
    min-width: 140px;
  }
  .sig-box {
    width: 150px;
    height: 52px;
    border: 1px solid #ccc;
    background: #fafafa;
    position: relative;
  }
  .sig-box::after {
    content: '';
    position: absolute;
    bottom: 10px;
    left: 10px;
    right: 10px;
    border-bottom: 1px solid #aaa;
  }
  .sig-label {
    font-family: Arial, sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: #333;
    text-transform: uppercase;
    letter-spacing: 0.6px;
  }
  .sig-role {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #888;
  }

  /* Seal */
  .seal-block {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
  }
  .seal-outer {
    width: 84px;
    height: 84px;
    border: 2px dashed #1a6b9a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .seal-inner {
    width: 68px;
    height: 68px;
    border: 1.5px solid #1a6b9a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
  }
  .seal-text {
    font-family: Arial, sans-serif;
    font-size: 6.5px;
    font-weight: 700;
    color: #1a6b9a;
    line-height: 1.5;
  }
  .seal-label {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #888;
  }

  /* Action buttons */
  .action-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    align-items: center;
    margin: 20px auto;
    padding: 15px;
    border-radius: 8px;
    width: 210mm;  /* page-এর width এর সমান করুন */
}
  .btn-action {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .btn-download {
    background: #28a745;
    color: white;
  }
  .btn-download:hover {
    background: #218838;
    transform: translateY(-1px);
  }
  .btn-print {
    background: #007bff;
    color: white;
  }
  .btn-print:hover {
    background: #0056b3;
    transform: translateY(-1px);
  }

  @media print {
    body { background: none; padding: 0; }
    .page { width: 216mm; height: 279mm; }
    .action-buttons { display: none !important; }
  }

  @page {
  size: A4;
  margin: 0;
}

@media print {
  /* Chrome/Edge specific */
  @media (-webkit-min-device-pixel-ratio: 0) {
    .border-top,
    .border-bottom,
    .border-left,
    .border-right {
      background-color: #1a6b9a !important;
      background-image: none !important;
    }
  }
}
</style>
</head>
<body>



<div class="action-buttons">
  <button onclick="printCertificate()" class="btn-action btn-print">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
      <polyline points="6 9 6 2 18 2 18 9"></polyline>
      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
      <rect x="6" y="14" width="12" height="8"></rect>
    </svg>
    Print Now
  </button>
</div>

<div class="page">

  <div class="border-top"></div>
  <div class="border-bottom"></div>
  <div class="border-left"></div>
  <div class="border-right"></div>
  <div class="inner-border"></div>

  <svg class="corner corner-tl" viewBox="0 0 44 44" fill="none"><polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75"/></svg>
  <svg class="corner corner-tr" viewBox="0 0 44 44" fill="none"><polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75"/></svg>
  <svg class="corner corner-bl" viewBox="0 0 44 44" fill="none"><polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75"/></svg>
  <svg class="corner corner-br" viewBox="0 0 44 44" fill="none"><polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75"/></svg>

  <div class="content">

    <!-- Header -->
    <div style="text-align: center; padding: 20px 0 5px 0;">
        <img class="invoice-logo" src="{{$img}}" alt=""
            style="width: auto; height: auto; max-width: 560px; max-height: none; margin-top: 15px;">
        <p style="border-color: #007bff; border-width: 3px;">
            ____________________________________________________________________</p>
    </div>

    <!-- Date -->
    <div class="date-row">
      <div class="date-box">DATE: @if($license){{ $license->getFormattedIssueDate() }}@else{{ now()->format('d/m/Y') }}@endif</div>
    </div>

    <!-- Centered content -->
    <div class="center-block">

      <div class="photo-box">
        @if($customer && !empty($customer->cust_image))
          <img src="{{ asset('storage/uploads/cust_image/' . $customer->cust_image) }}"
               alt="{{ $customer->name }}"
               class="user-photo"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <div class="photo-placeholder" style="display:none;">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
              <circle cx="28" cy="20" r="12" fill="#c8dce8"/>
              <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3"/>
            </svg>
          </div>
        @elseif($customer)
          <!-- Try default customer image if customer exists but no specific cust_image -->
          <img src="{{ asset('storage/uploads/cust_image/default.png') }}"
               alt="{{ $customer->name }}"
               class="user-photo"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <div class="photo-placeholder" style="display:none;">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
              <circle cx="28" cy="20" r="12" fill="#c8dce8"/>
              <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3"/>
            </svg>
          </div>
        @else
          <div class="photo-placeholder">
            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
              <circle cx="28" cy="20" r="12" fill="#c8dce8"/>
              <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3"/>
            </svg>
          </div>
        @endif
      </div>

      <div class="grade-box">Grade @if($license){{ $license->grade }}@else D @endif</div>
    <div class="grade-box"> @if($license){{ $license->type }}@else Baabuur @endif</div>


    </div>
<div class="invoice-body">
            <table class="invoice-summary" style="margin-top: 10px;">
                <tbody></tbody>
                    <tr>
                        <td><h3 style="margin-top: 0px;">Full Name / Magac : - </h3></td>
                        <td><h3 style="margin-top: 0px;">{{!empty($customer->name) ? $customer->name : '-'}}</h3></td>
                    </tr>
                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Mother Name <br> Magaca Hooyo : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->mother_name) ? $customer->mother_name : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Gender <br> Jinsiga : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->gender) ? $customer->gender : '-'}}</h3>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Date of Birth <br> Taariikhka Dhalashada : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->dob) ? $customer->dob : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Place of Birth <br> Goobta Dhalashada : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->pob) ? $customer->pob : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Address <br> Deeganka : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->billing_address) ? $customer->billing_address : '-'}} ,{{!empty($customer->billing_city) ? $customer->billing_city : '-'}} , <br>{{!empty($customer->billing_state) ? $customer->billing_state : '-'}} , {{!empty($customer->billing_country) ? $customer->billing_country : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Mobile <br> Telefon: - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->contact) ? $customer->contact : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Email  : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->email) ? $customer->email : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Health Body <br> Cafimaadka Jirka : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->body) ? $customer->body : '-'}}</h3>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <h4 style="margin-top: 10px;">Eye <br> Indhaha : - </h3>
                        </td>
                        <td>
                            <h4 style="margin-top: 10px;">{{!empty($customer->eye) ? $customer->eye : '-'}}</h3>
                        </td>
                    </tr>

                </tfoot>
            </table>
        </div>

    <!-- Signature section -->
    <div class="signature-section">

      <div class="sig-block">
        <div class="sig-box"></div>
        <div class="sig-label">Director</div>
        <div class="sig-role">Authorized Signature</div>
      </div>

      <div class="seal-block">
        <div class="seal-outer">
          <div class="seal-inner">
            <div class="seal-text">KALSAN<br>DRIVING<br>SCHOOLS<br>OFFICIAL<br>SEAL</div>
          </div>
        </div>
        <div class="seal-label"></div>
      </div>

      <div class="sig-block">
        <div class="sig-box"></div>
        <div class="sig-label">secretary</div>
        <div class="sig-role">Authorized Signature</div>
      </div>

    </div>

  </div>
</div>



<script>

/* function printCertificate() {
  // Hide the action buttons temporarily
  const actionButtons = document.querySelector('.action-buttons');
  const originalDisplay = actionButtons.style.display;
  actionButtons.style.display = 'none';

  // Trigger print dialog
  window.print();

  // Restore the action buttons after print dialog closes
  setTimeout(() => {
    actionButtons.style.display = originalDisplay;
  }, 100);
} */
    function printCertificate() {
        const actionButtons = document.querySelector('.action-buttons');
        const originalDisplay = actionButtons.style.display;
        actionButtons.style.display = 'none';
        window.print();
        setTimeout(() => {
            actionButtons.style.display = originalDisplay;
        }, 100);
    }

    // Check if content fits A4 perfectly
    function checkA4Fit() {
        const page = document.querySelector('.page');
        const content = document.querySelector('.content');

        if (page && content) {
            const pageHeight = page.scrollHeight;
            const contentHeight = content.scrollHeight;
            const difference = pageHeight - contentHeight;

            if (difference > 0) {
                console.log(`✓ Perfect fit! Extra space: ${difference}px`);
            } else if (difference < 0) {
                console.warn(`⚠ Content overflows by: ${Math.abs(difference)}px`);
            } else {
                console.log('✓ Exact fit!');
            }
        }
    }

    // Run check when page loads
    window.addEventListener('load', checkA4Fit);
</script>

{{-- @if(!isset($preview))
   @include('invoice.script');
@endif --}}


</body>
</html>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=2.0">
    <title>Kalsan Driving Certificate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #d0d0d0;
            display: flex;
            flex-direction: column;
            /* এটা যোগ করুন */
            justify-content: flex-start;
            align-items: center;
            /* center করুন */
            min-height: 100vh;
            padding: 10px;
            font-family: Georgia, 'Times New Roman', serif;
        }

        .page {
            width: 210mm;
            min-height: 300mm;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .border-top,
        .border-bottom {
            position: absolute;
            left: 0;
            right: 0;
            height: 20px;
            background: repeating-linear-gradient(90deg, #1a6b9a 0px, #1a6b9a 9px, #5ab0d8 9px, #5ab0d8 18px);
            z-index: 10;
        }

        .border-top {
            top: 0;
        }

        .border-bottom {
            bottom: 0;
        }

        .border-left,
        .border-right {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 20px;
            background: repeating-linear-gradient(180deg, #1a6b9a 0px, #1a6b9a 9px, #5ab0d8 9px, #5ab0d8 18px);
            z-index: 10;
        }

        .border-left {
            left: 0;
        }

        .border-right {
            right: 0;
        }

        .inner-border {
            position: absolute;
            top: 26px;
            left: 26px;
            right: 26px;
            bottom: 26px;
            border: 1.5px solid #1a6b9a;
            z-index: 5;
            pointer-events: none;
        }

        .corner {
            position: absolute;
            width: 44px;
            height: 44px;
            z-index: 12;
        }

        .corner-tl {
            top: 4px;
            left: 4px;
        }

        .corner-tr {
            top: 4px;
            right: 4px;
        }

        .corner-bl {
            bottom: 4px;
            left: 4px;
        }

        .corner-br {
            bottom: 4px;
            right: 4px;
        }

        /* Main flex layout */
        .content {
            flex: 1;
            position: relative;
            z-index: 6;
            padding: 20px 40px 20px 40px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 7px;
            flex-shrink: 0;
        }

        .logo-name {
            font-family: Arial, sans-serif;
            font-weight: 900;
            font-size: 28px;
            color: #1a6b9a;
            line-height: 1;
            letter-spacing: 1px;
        }

        .logo-sub {
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #1a6b9a;
            line-height: 1.35;
        }

        .center-logo {
            width: 70px;
            height: 70px;
            border: 2.5px solid #1a6b9a;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .center-logo-text {
            font-family: Arial, sans-serif;
            font-size: 6px;
            font-weight: 700;
            color: #1a6b9a;
            text-align: center;
            line-height: 1.3;
        }

        .arabic-main {
            font-family: Arial, sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #1a6b9a;
            direction: rtl;
            line-height: 1.1;
            text-align: right;
        }

        .arabic-sub {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1a6b9a;
            direction: rtl;
            text-align: right;
        }

        /* Date */
        .date-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 4px;
            flex-shrink: 0;
        }

        .date-box {
            border: 1px solid #666;
            padding: 3px 12px;
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
        }

        /* CENTER — fills remaining vertical space */
        .center-block {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 0;
            /* important for flex children to prevent overflow */
            gap: 0;
        }

        .cert-title {
            font-family: Arial, sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: #1a9ad7;
            line-height: 1;
            margin-bottom: 5px;
        }

        .cert-subtitle {
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-style: italic;
            font-weight: 700;
            color: #1a6b9a;
            text-decoration: underline;
            margin-bottom: 16px;
        }

        .photo-box {
            border: 2px solid #1a9ad7;
            width: 130px;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eaf5fb;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .user-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 2px;
        }

        .photo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .company-logo {
            max-height: 100px;
            max-width: 220px;
            object-fit: contain;
        }

        .grade-box {
            border: 1.5px solid #1a9ad7;
            padding: 3px 52px;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin-bottom: 14px;
        }

        .holder-name {
            font-family: Arial, sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #1a6b9a;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .license-no {
            font-family: Arial, sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #222;
            margin-bottom: 8px;
        }

        .license-no span {
            color: #cc2200;
        }

        .serial {
            font-family: Arial, sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #222;
            margin-bottom: 16px;
        }

        .serial span {
            color: #cc2200;
        }

        .cert-body {
            font-style: italic;
            font-size: 14px;
            color: #222;
            line-height: 2;
        }

        .cert-body .ul {
            text-decoration: underline;
        }

        /* SIGNATURE SECTION */
        .signature-section {
            flex-shrink: 0;
            border-top: 1.5px dashed #bbb;
            padding-top: 16px;
            padding-bottom: 4px;
            display: flex;
            margin-top: auto;
            align-items: flex-end;
            justify-content: space-between;
            align-items: flex-start;
        }

        .sig-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            min-width: 140px;
        }

        .sig-box {
            width: 150px;
            height: 52px;
            border: 1px solid #ccc;
            background: #fafafa;
            position: relative;
        }

        .sig-box::after {
            content: '';
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            border-bottom: 1px solid #aaa;
        }

        .sig-label {
            font-family: Arial, sans-serif;
            font-size: 11px;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .sig-role {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #888;
        }

        /* Seal */
        .seal-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .seal-outer {
            width: 84px;
            height: 84px;
            border: 2px dashed #1a6b9a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .seal-inner {
            width: 68px;
            height: 68px;
            border: 1.5px solid #1a6b9a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .seal-text {
            font-family: Arial, sans-serif;
            font-size: 6.5px;
            font-weight: 700;
            color: #1a6b9a;
            line-height: 1.5;
        }

        .seal-label {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #888;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            padding: 15px;
            border-radius: 8px;
            width: 210mm;
            /* page-এর width এর সমান করুন */
        }

        .btn-action {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-download {
            background: #28a745;
            color: white;
        }

        .btn-download:hover {
            background: #218838;
            transform: translateY(-1px);
        }

        .btn-print {
            background: #007bff;
            color: white;
        }

        .btn-print:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }

            .page {
                width: 216mm;
                height: 279mm;
            }

            .action-buttons {
                display: none !important;
            }
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            /* Chrome/Edge specific */
            @media (-webkit-min-device-pixel-ratio: 0) {

                .border-top,
                .border-bottom,
                .border-left,
                .border-right {
                    background-color: #1a6b9a !important;
                    background-image: none !important;
                }
            }
        }
    </style>
</head>

<body>



    <div class="action-buttons">
        {{-- <button onclick="printCertificate()" class="btn-action btn-print">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Now
        </button> --}}
    </div>

    <div class="page">

        <div class="border-top"></div>
        <div class="border-bottom"></div>
        <div class="border-left"></div>
        <div class="border-right"></div>
        <div class="inner-border"></div>

        <svg class="corner corner-tl" viewBox="0 0 44 44" fill="none">
            <polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75" />
        </svg>
        <svg class="corner corner-tr" viewBox="0 0 44 44" fill="none">
            <polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75" />
        </svg>
        <svg class="corner corner-bl" viewBox="0 0 44 44" fill="none">
            <polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75" />
        </svg>
        <svg class="corner corner-br" viewBox="0 0 44 44" fill="none">
            <polygon points="22,2 25,15 38,15 28,24 31,37 22,29 13,37 16,24 6,15 19,15" fill="#1a6b9a" opacity="0.75" />
        </svg>

        <div class="content">

            <!-- Header -->
            <div style="text-align: center; padding: 20px 0 5px 0;">
                <img class="invoice-logo" src="{{$img}}" alt=""
                    style="width: auto; height: auto; max-width: 560px; max-height: none; margin-top: 15px;">
                <p style="border-color: #007bff; border-width: 3px;">
                    ____________________________________________________________________</p>
            </div>

            <!-- Date -->
            <div class="date-row">
                <div class="date-box">DATE:
                    @if($license){{ $license->getFormattedIssueDate() }}@else{{ now()->format('d/m/Y') }}@endif</div>
            </div>

            <!-- Centered content -->
            <div class="center-block">
                <div class="cert-title">
                    <h2 style="color: #007bff">(Certificate)</h2>
                </div>
                <div class="cert-subtitle">
                    <h2 style="color: #007bff">Driving Course Completion</h2>
                </div>

                <div class="photo-box">
                    @if($customer && !empty($customer->cust_image))
                        <img src="{{ asset('storage/uploads/cust_image/' . $customer->cust_image) }}"
                            alt="{{ $customer->name }}" class="user-photo"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="photo-placeholder" style="display:none;">
                            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                                <circle cx="28" cy="20" r="12" fill="#c8dce8" />
                                <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3" />
                            </svg>
                        </div>
                    @elseif($customer)
                        <!-- Try default customer image if customer exists but no specific cust_image -->
                        <img src="{{ asset('storage/uploads/cust_image/default.png') }}" alt="{{ $customer->name }}"
                            class="user-photo"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="photo-placeholder" style="display:none;">
                            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                                <circle cx="28" cy="20" r="12" fill="#c8dce8" />
                                <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3" />
                            </svg>
                        </div>
                    @else
                        <div class="photo-placeholder">
                            <svg width="56" height="56" viewBox="0 0 56 56" fill="none">
                                <circle cx="28" cy="20" r="12" fill="#c8dce8" />
                                <path d="M6 54c0-12.2 9.8-22 22-22s22 9.8 22 22" fill="#d8eaf3" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="grade-box">Grade @if($license){{ $license->grade }}@else D @endif</div>

                <div class="holder-name">@if($customer){{ strtoupper($customer->name) }}@else CUSTOMER NAME @endif</div>
                <div class="license-no">Serial No:
                    <span>{{ \App\Models\User::find($invoice->created_by)->invoiceNumberFormat($invoice->invoice_id) }}</span>
                </div>
                <div class="serial"> <span></span></div>

                <div class="cert-body">
                    <h2>This is to certify that</h2>
                    <h2 style="margin-left: 35px;">The person named above has successfully completed proficiency driving
                        skills, road
                        safety, and
                        traffic regulations.</h2>

                </div>
            </div>

            <!-- Signature section -->
            <div class="signature-section">

                <div class="sig-block">
                    <div class="sig-box"></div>
                    <div class="sig-label">Director</div>
                    <div class="sig-role">Authorized Signature</div>
                </div>

                <div class="seal-block">
                    <div class="seal-outer">
                        <div class="seal-inner">
                            <div class="seal-text">KALSAN<br>DRIVING<br>SCHOOLS<br>OFFICIAL<br>SEAL</div>
                        </div>
                    </div>
                    <div class="seal-label"></div>
                </div>

                <div class="sig-block">
                    <div class="sig-box"></div>
                    <div class="sig-label">secretary</div>
                    <div class="sig-role">Authorized Signature</div>
                </div>

            </div>

        </div>
    </div>



    <script>

        /* function printCertificate() {
          // Hide the action buttons temporarily
          const actionButtons = document.querySelector('.action-buttons');
          const originalDisplay = actionButtons.style.display;
          actionButtons.style.display = 'none';

          // Trigger print dialog
          window.print();

          // Restore the action buttons after print dialog closes
          setTimeout(() => {
            actionButtons.style.display = originalDisplay;
          }, 100);
        } */
        function printCertificate() {
            const actionButtons = document.querySelector('.action-buttons');
            const originalDisplay = actionButtons.style.display;
            actionButtons.style.display = 'none';
            window.print();
            setTimeout(() => {
                actionButtons.style.display = originalDisplay;
            }, 100);
        }

        // Check if content fits A4 perfectly
        function checkA4Fit() {
            const page = document.querySelector('.page');
            const content = document.querySelector('.content');

            if (page && content) {
                const pageHeight = page.scrollHeight;
                const contentHeight = content.scrollHeight;
                const difference = pageHeight - contentHeight;

                if (difference > 0) {
                    console.log(`✓ Perfect fit! Extra space: ${difference}px`);
                } else if (difference < 0) {
                    console.warn(`⚠ Content overflows by: ${Math.abs(difference)}px`);
                } else {
                    console.log('✓ Exact fit!');
                }
            }
        }

        // Run check when page loads
        window.addEventListener('load', checkA4Fit);
    </script>

    {{-- @if(!isset($preview))
    @include('invoice.script');
    @endif --}}


</body>

</html>
