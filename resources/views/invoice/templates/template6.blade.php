<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>Mogadishu Municipality | Certificate of Identity Confirmation</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(145deg, #d4cfc4 0%, #b8b2a2 100%);
      font-family: 'Segoe UI', 'Roboto', 'Poppins', system-ui, -apple-system, 'BlinkMacSystemFont', 'Georgia', serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem 1.5rem;
    }

    /* main certificate card */
    .certificate {
      max-width: 980px;
      width: 100%;
      background: #fffef7;
      background-image: radial-gradient(circle at 10% 20%, rgba(200,180,130,0.05) 2%, transparent 2.5%);
      background-size: 28px 28px;
      border-radius: 32px;
      box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.4), 0 4px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.2s ease;
    }

    .certificate:hover {
      transform: scale(1.01);
    }

    /* ornate border & inner glow */
    .certificate-inner {
      padding: 2rem 2rem 2rem 2rem;
      border: 1px solid #e9dfb0;
      margin: 1.2rem;
      border-radius: 24px;
      background: rgba(255, 253, 240, 0.6);
      box-shadow: inset 0 0 0 1px #fff8e7, 0 0 0 2px #cbae76;
    }

    /* header section with bilingual titles */
    .header {
      text-align: center;
      margin-bottom: 1.8rem;
      border-bottom: 2px solid #c2a15b;
      padding-bottom: 1rem;
    }

    .seal-icon {
      font-size: 2.2rem;
      letter-spacing: 4px;
      color: #8b6b3c;
      margin-bottom: 0.5rem;
    }

    .title-so {
      font-size: 1.9rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: #2c3e2f;
      text-transform: uppercase;
      font-family: 'Segoe UI', 'Times New Roman', serif;
      background: linear-gradient(135deg, #2c5e2a, #4a6b2f);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
      text-shadow: 0 1px 1px rgba(0,0,0,0.05);
    }

    .title-en {
      font-size: 1rem;
      font-weight: 500;
      color: #6a4e2a;
      letter-spacing: 0.5px;
      border-top: 1px dashed #d9bc7a;
      display: inline-block;
      margin-top: 6px;
      padding-top: 6px;
      text-transform: uppercase;
    }

    .mayor-statement {
      background: #faf3e0;
      padding: 0.7rem 1rem;
      border-radius: 60px;
      margin: 1rem 0 1.2rem 0;
      font-size: 0.9rem;
      text-align: center;
      color: #3a2a1c;
      border-left: 4px solid #c49a6c;
      border-right: 4px solid #c49a6c;
      font-weight: 500;
    }

    /* two-column layout for main details */
    .details-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.2rem 2rem;
      margin: 1.5rem 0 1.2rem;
    }

    .detail-item {
      display: flex;
      flex-wrap: wrap;
      align-items: baseline;
      border-bottom: 1px dotted #e2d4bb;
      padding: 0.4rem 0;
    }

    .detail-label {
      font-weight: 700;
      min-width: 130px;
      width: 38%;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      color: #7a5a3a;
      font-family: 'Segoe UI', monospace;
    }

    .detail-value {
      font-weight: 600;
      font-size: 0.98rem;
      color: #1f2e1c;
      word-break: break-word;
      flex: 1;
      font-family: 'Segoe UI', 'Roboto', sans-serif;
    }

    /* special highlight for full name */
    .detail-value.name-highlight {
      font-size: 1.1rem;
      font-weight: 800;
      color: #8b3c1c;
      letter-spacing: 0.3px;
    }

    /* thumbprint & picture section (simulated modern) */
    .bio-section {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      margin: 1.8rem 0 1.2rem;
      background: #fef9ef;
      padding: 1rem 1.2rem;
      border-radius: 28px;
      border: 1px solid #eadbbe;
      align-items: center;
      justify-content: space-between;
    }

    .photo-placeholder {
      flex: 1;
      min-width: 130px;
      text-align: center;
    }

    .photo-frame {
      width: 130px;
      height: 150px;
      background: #f0e3ce;
      border: 3px solid #c6a15e;
      border-radius: 12px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 12px rgba(0,0,0,0.1);
      background: linear-gradient(145deg, #faf1e2, #efe0cc);
    }

    .photo-icon {
      font-size: 3.8rem;
      opacity: 0.7;
    }

    .photo-caption {
      font-size: 0.7rem;
      margin-top: 6px;
      font-weight: 600;
      color: #886e42;
      text-transform: uppercase;
    }

    .thumb-area {
      flex: 1;
      text-align: center;
      min-width: 130px;
    }

    .thumb-print {
      background: #e6d7c0;
      width: 130px;
      height: 100px;
      margin: 0 auto;
      border-radius: 40px 20px 50px 30px;
      background: repeating-radial-gradient(circle at 20% 35%, #a5834e 1px, #d6bc8a 2px, #f5e8d4 6px);
      position: relative;
      box-shadow: inset 0 0 0 2px #ba8e48, 0 6px 10px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      color: #5a3e1f;
    }

    .thumb-print span {
      background: rgba(255,250,220,0.7);
      padding: 6px 8px;
      border-radius: 50px;
      font-size: 0.8rem;
      font-weight: bold;
    }

    /* mayor signature area */
    .mayor-section {
      margin-top: 1.8rem;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      border-top: 2px solid #ddceaa;
      padding-top: 1.4rem;
    }

    .signature-block {
      text-align: center;
    }

    .signature-line {
      font-family: 'Brush Script MT', cursive, 'Segoe Script', serif;
      font-size: 1.5rem;
      color: #3b3a2c;
      border-bottom: 1px solid #b19664;
      min-width: 180px;
      padding-bottom: 4px;
      margin-bottom: 6px;
    }

    .mayor-name {
      font-weight: 700;
      font-size: 0.85rem;
      color: #4b3a22;
    }

    .date-issue {
      text-align: right;
      font-style: italic;
      background: #fff5e6;
      padding: 0.5rem 1rem;
      border-radius: 32px;
    }

    .footer-stamp {
      margin-top: 1.2rem;
      font-size: 0.7rem;
      text-align: center;
      color: #a18456;
      border-top: 1px solid #e5d7bb;
      padding-top: 1rem;
      display: flex;
      justify-content: space-between;
    }

    /* responsive */
    @media (max-width: 700px) {
      .certificate-inner {
        padding: 1rem;
        margin: 0.8rem;
      }
      .details-grid {
        grid-template-columns: 1fr;
        gap: 0.4rem;
      }
      .detail-label {
        min-width: 110px;
        width: auto;
      }
      .bio-section {
        flex-direction: column;
        gap: 1rem;
      }
      .mayor-section {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
      }
      .date-issue {
        text-align: center;
      }
      .title-so {
        font-size: 1.5rem;
      }
    }

    /* utility */
    .text-muted {
      font-weight: normal;
      font-size: 0.7rem;
      color: #ab8b58;
    }
  </style>
</head>
<body>
<div class="certificate">
  <div class="certificate-inner">
    <!-- Header area -->
    <div class="header">
      <div class="seal-icon">
        ⚜️ 𓋴  𓂀  🕌
      </div>
      <div class="title-so">
        DOWLADDA HOOSE EE MUQDISHO
      </div>
      <div class="title-en">
        MUNICIPALITY OF MOGADISHU
      </div>
      <div style="font-size: 1.1rem; font-weight: 600; margin: 8px 0 0; letter-spacing: 1px;">
        WARQADDA SUGNAANTA
      </div>
      <div style="font-size: 0.8rem; font-weight: 500; color: #6d5436;">
        Certificate of Identity Confirmation
      </div>
    </div>

    <!-- Mayor certification statement (bilingual style) -->
    <div class="mayor-statement">
      <span style="font-weight:800;">DUQA MAGAALADA MUQDISHO WUXUU CADEYNAYA</span> — The Mayor of Mogadishu hereby certifies that the person whose picture and thumb print appears below has the following details:
    </div>

    <!-- 2-column personal details (matches document fields) -->
    <div class="details-grid">
      <div class="detail-item">
        <span class="detail-label">MAGACA / Full Name</span>
        <span class="detail-value name-highlight">MOHAMED ABDI MOHAMED</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">TAARIIKHDA DHALASHADA</span>
        <span class="detail-value">27-Jun-2005</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">GOOBTA DHALASHADA</span>
        <span class="detail-value">JILIB</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">NUMBERKA KAARKAAQOONSIGA</span>
        <span class="detail-value">733113</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">JINSI / Gender</span>
        <span class="detail-value">Male</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">XAALADDA GUURKA</span>
        <span class="detail-value">Single</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">DEGGAN / Address</span>
        <span class="detail-value">Dharkenley</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">MAGACA HOOYADA</span>
        <span class="detail-value">HALIMO MOHAMED FARAH</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">TAARIIKHDA LA BIXIYAY</span>
        <span class="detail-value">17-Nov-2024</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">SHAQADA / Occupation</span>
        <span class="detail-value">LABOURER</span>
      </div>
    </div>

    <!-- Picture + Thumbprint section (exactly as described in the certificate: "sawirka iyo suul saaridda") -->
    <div class="bio-section">
      <div class="photo-placeholder">
        <div class="photo-frame">
          <div class="photo-icon">🖼️📸</div>
          <div style="font-size: 0.7rem; font-weight: bold; background:#fff0cf; padding:2px 8px; border-radius:20px;">OFFICIAL PORTRAIT</div>
        </div>
        <div class="photo-caption">Sawirka Qofka / Person's Picture</div>
      </div>
      <div class="thumb-area">
        <div class="thumb-print">
          <span>🖐️🔘 SUUL SAARIDDA</span>
        </div>
        <div class="photo-caption" style="margin-top:8px;">Thumbprint / Fingerprint Mark</div>
        <div class="text-muted" style="font-size: 0.65rem;">(Right thumb impression)</div>
      </div>
      <div style="flex:0.5; min-width: 80px; font-size: 0.75rem; text-align:center; background:#f1ecdf; border-radius:24px; padding:6px;">
        ✅ ID Verified <br> ⚡ Biometric
      </div>
    </div>

    <!-- Mayor signature and official stamp area -->
    <div class="mayor-section">
      <div class="signature-block">
        <div class="signature-line">Dr. Xasan Maxamed Xuseen</div>
        <div class="mayor-name">DUQA MAGAALADA EE MUQDISHO</div>
        <div style="font-size: 0.7rem; color:#785d36;">Mayor of Mogadishu</div>
      </div>
      <div class="date-issue">
        <span style="font-weight:600;">Date of Issue: </span>17-Nov-2024<br>
        <span style="font-size:0.7rem;">Warqadda Sugnaanta · Identity Certificate</span>
      </div>
    </div>
    
    <!-- Official stamp / footer with holographic effect -->
    <div class="footer-stamp">
      <span>🔖 Registration No: MOG/ID/733113/2024</span>
      <span>⚜️ SEAL OF THE MUNICIPALITY ⚜️</span>
      <span>📅 Expiry: —— (Permanent Identity)</span>
    </div>
    <div style="text-align: center; margin-top: 0.7rem; font-size: 0.7rem; color: #ad8f62; border-top: 0; padding-top: 0;">
      <span>✦ This document is issued under the authority of the Benadir Regional Administration ✦</span>
    </div>
  </div>
</div>
</body>
</html>