<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'DejaVu Sans', Arial, sans-serif;
        background: #fff;
        color: #1a1a2e;
        width: 297mm;
        height: 210mm;
    }

    .certificate {
        width: 100%;
        height: 100%;
        position: relative;
        padding: 20mm 22mm;
        border: 12px solid #0056d2;
        box-shadow: inset 0 0 0 4px #e8f0fe;
    }

    .corner {
        position: absolute;
        width: 60px;
        height: 60px;
        border-color: #0056d2;
        border-style: solid;
    }
    .corner-tl { top: 10px; left: 10px; border-width: 4px 0 0 4px; }
    .corner-tr { top: 10px; right: 10px; border-width: 4px 4px 0 0; }
    .corner-bl { bottom: 10px; left: 10px; border-width: 0 0 4px 4px; }
    .corner-br { bottom: 10px; right: 10px; border-width: 0 4px 4px 0; }

    .header { text-align: center; margin-bottom: 14px; }
    .logo-name {
        font-size: 36px;
        font-weight: bold;
        color: #0056d2;
        letter-spacing: 4px;
        text-transform: uppercase;
    }
    .logo-sub { font-size: 11px; color: #6b7280; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }

    .divider {
        width: 80px;
        height: 3px;
        background: linear-gradient(90deg, #0056d2, #10b981);
        margin: 12px auto;
        border-radius: 2px;
    }

    .certifies { text-align: center; font-size: 13px; color: #6b7280; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 8px; }

    .student-name {
        text-align: center;
        font-size: 38px;
        font-weight: bold;
        color: #0056d2;
        margin: 8px 0;
        border-bottom: 2px solid #e8f0fe;
        padding-bottom: 8px;
    }

    .course-label { text-align: center; font-size: 12px; color: #6b7280; margin: 10px 0 4px; letter-spacing: 1px; text-transform: uppercase; }
    .course-name { text-align: center; font-size: 22px; font-weight: bold; color: #1a1a2e; margin-bottom: 10px; }

    .meta { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 16px; }

    .meta-left { text-align: left; }
    .meta-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
    .meta-value { font-size: 13px; font-weight: bold; color: #1a1a2e; margin-top: 2px; }

    .meta-center { text-align: center; }

    .seal {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid #0056d2;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        text-align: center;
        padding: 8px;
    }
    .seal-text { font-size: 9px; font-weight: bold; color: #0056d2; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.3; }

    .meta-right { text-align: right; }

    .signature-line { width: 120px; height: 1px; background: #d1d5db; margin-left: auto; margin-bottom: 4px; }

    .qr-section { text-align: right; }
    .qr-section img { width: 80px; height: 80px; }
    .qr-label { font-size: 8px; color: #9ca3af; margin-top: 2px; }

    .footer-text {
        text-align: center;
        font-size: 9px;
        color: #9ca3af;
        margin-top: 14px;
        letter-spacing: 0.5px;
    }
</style>
</head>
<body>
<div class="certificate">
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <div class="header">
        <div class="logo-name">CLARIX</div>
        <div class="logo-sub">Plateforme E-Learning</div>
    </div>

    <div class="divider"></div>

    <div class="certifies">Certifie que</div>

    <div class="student-name">{{ $enrollment->user->name }}</div>

    <div class="course-label">a complété avec succès la formation</div>
    <div class="course-name">« {{ $enrollment->course->title }} »</div>

    <div class="meta">
        <div class="meta-left">
            <div class="meta-label">Date d'obtention</div>
            <div class="meta-value">{{ $completedAt->format('d F Y') }}</div>
            <div style="margin-top: 12px;">
                <div class="meta-label">Identifiant</div>
                <div class="meta-value" style="font-size:10px; color:#6b7280;">#{{ str_pad($enrollment->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="meta-center">
            <div class="seal">
                <div class="seal-text">Formation<br>Accomplie<br>✓</div>
            </div>
        </div>

        <div class="meta-right">
            <div class="qr-section">
                <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code">
                <div class="qr-label">Vérifier le certificat</div>
            </div>
        </div>
    </div>

    <div class="footer-text">
        Ce certificat a été généré automatiquement par la plateforme CLARIX · {{ $verifyUrl }}
    </div>
</div>
</body>
</html>
