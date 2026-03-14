<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Penugasan - {{ $assignment->slug }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24pt;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header h2 {
            margin: 5px 0 0;
            font-size: 14pt;
            font-weight: normal;
        }

        .meta-info {
            margin-bottom: 30px;
            width: 100%;
        }

        .meta-info td {
            padding: 5px 0;
            vertical-align: top;
        }

        .label {
            width: 150px;
            font-weight: bold;
        }

        .content {
            margin-bottom: 40px;
            text-align: justify;
        }

        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
            padding-bottom: 5px;
            text-transform: uppercase;
            font-size: 10pt;
            color: #555;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
        }

        .signature {
            float: right;
            width: 250px;
            text-align: center;
        }

        .signature-line {
            margin-top: 80px;
            border-top: 1px solid #333;
        }

        .timestamp {
            font-size: 9pt;
            color: #999;
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #333;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Surat Penugasan</h1>
        <h2>Redaksi SIK-MSA</h2>
    </div>

    <table class="meta-info">
        <tr>
            <td class="label">Nomor Surat</td>
            <td>: #{{ substr($assignment->id, 0, 8) }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Cetak</td>
            <td>: {{ now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td>: Penugasan Liputan Lapangan</td>
        </tr>
        <tr>
            <td class="label">Status</td>
            <td>
                : <span class="status-badge">{{ $assignment->status }}</span>
            </td>
        </tr>
    </table>

    <div class="content">
        <p>Dengan ini redaksi memberikan tugas kepada wartawan di bawah ini:</p>

        <table style="width: 100%; margin: 20px 0;">
            <tr>
                <td class="label">Nama</td>
                <td>: <strong>{{ $assignment->reporter->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>: {{ $assignment->reporter->email ?? '-' }}</td>
            </tr>
        </table>

        <p>Untuk melaksanakan liputan dengan detail sebagai berikut:</p>

        <div style="background: #f9f9f9; padding: 15px; border: 1px solid #ddd; margin: 15px 0;">
            <div class="section-title">Judul Liputan</div>
            <div style="font-size: 14pt; font-weight: bold; margin-bottom: 10px;">{{ $assignment->title }}</div>

            <div class="section-title">Lokasi & Waktu</div>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 100px;"><strong>Lokasi</strong></td>
                    <td>: {{ $assignment->location_name }}</td>
                </tr>
                <tr>
                    <td><strong>Deadline</strong></td>
                    <td>: {{ $assignment->deadline->translatedFormat('d F Y, H:i') }} WIB</td>
                </tr>
            </table>

            <div class="section-title" style="margin-top: 15px;">Deskripsi / Brief</div>
            <div style="white-space: pre-line;">{{ $assignment->description }}</div>
        </div>

        <p>Demikian surat penugasan ini dibuat untuk dilaksanakan dengan sebaik-baiknya dan penuh tanggung jawab.</p>
    </div>

    <table class="footer">
        <tr>
            <td></td>
            <td>
                <div class="signature">
                    <p>Ditetapkan di Jakarta,</p>
                    <p>{{ now()->translatedFormat('d F Y') }}</p>
                    <br><br><br>
                    <div class="signature-line">
                        <strong>Redaktur Pelaksana</strong>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="timestamp">
        Dicetak oleh sistem pada {{ now() }}
    </div>

</body>

</html>