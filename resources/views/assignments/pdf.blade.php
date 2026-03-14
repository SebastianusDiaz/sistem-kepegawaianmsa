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
            <td class="label">Tanggal Cetak</td>
            <td>: {{ now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Perihal</td>
            <td>: <strong>Penugasan Liputan Lapangan</strong></td>
        </tr>

    </table>

    <div class="content">
        <p>Dengan ini redaksi memberikan tugas kepada wartawan di bawah ini:</p>

        <table class="meta-info">
            <tr>
                <td class="label">Nama</td>
                <td>: <strong>{{ $assignment->reporter->name ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td>: {{ $assignment->reporter->profile->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td>: {{ $assignment->reporter->profile->position->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Divisi</td>
                <td>: {{ $assignment->reporter->profile->division->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tgl Lahir</td>
                <td>: {{ $assignment->reporter->profile->birth_place ?? '-' }},
                    {{ $assignment->reporter->profile->birth_date ? \Carbon\Carbon::parse($assignment->reporter->profile->birth_date)->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td>:
                    {{ $assignment->reporter->profile->gender === 'male' ? 'Laki-laki' : ($assignment->reporter->profile->gender === 'female' ? 'Perempuan' : '-') }}
                </td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>: {{ $assignment->reporter->profile->address ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">No. Handphone</td>
                <td>: {{ $assignment->reporter->profile->phone ?? '-' }}</td>
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

                    <div style="height: 80px; margin-top: 20px; text-align: center;">
                        @if($director && $director->profile && $director->profile->signature_path)
                            <img src="{{ public_path('storage/' . $director->profile->signature_path) }}"
                                style="height: 80px;">
                        @else
                            <div style="height: 80px;"></div>
                        @endif
                    </div>

                    <div class="signature-line" style="margin-top: 5px;">
                        <strong>{{ $director->name ?? 'Direktur Utama' }}</strong><br>
                        <span
                            style="font-weight: normal; font-size: 10pt;">{{ $director->profile->position->name ?? 'Direktur' }}</span>
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