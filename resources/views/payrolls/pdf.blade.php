<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $payroll->period_end->format('F Y') }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a202c;
            text-transform: uppercase;
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            color: #4a5568;
        }

        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-table td {
            padding: 5px;
        }

        .label {
            font-weight: bold;
            width: 120px;
            color: #4a5568;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table th {
            background: #f7fafc;
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
            text-transform: uppercase;
            color: #4a5568;
        }

        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #edf2f7;
        }

        .amount {
            text-align: right;
            font-family: monospace;
        }

        .total-section {
            margin-top: 30px;
            background: #ffff;
            border: 1px solid #e2e8f0;
            padding: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #1a202c;
            border-top: 2px solid #2d3748;
            padding-top: 10px;
            margin-top: 10px;
            text-align: right;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #718096;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .sig-box {
            width: 200px;
            text-align: center;
            float: right;
        }

        .sig-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">Media Sriwijaya (SI-PEG)</div>
        <div class="document-title">SLIP GAJI KARYAWAN</div>
        <div>Periode: {{ $payroll->period_end->translatedFormat('F Y') }}</div>
        <div style="font-size: 12px; margin-top: 5px;">ID Transaksi:
            #PAY-{{ $payroll->id }}-{{ $payroll->period_end->format('mY') }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama</td>
            <td>: {{ $payroll->user->name }}</td>
            <td class="label">Jabatan</td>
            <td>: {{ $payroll->user->profile->position->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIP/ID</td>
            <td>: {{ $payroll->user->id }}</td>
            <td class="label">Divisi</td>
            <td>: {{ $payroll->user->profile->division->name ?? '-' }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th style="text-align: right">Penerimaan</th>
                <th style="text-align: right">Potongan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="amount">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</td>
                <td></td>
            </tr>
            @foreach($payroll->deduction_details ?? [] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="amount">
                        @if(($item['type'] ?? '') == 'bonus')
                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="amount">
                        @if(($item['type'] ?? '') == 'deduction')
                            Rp {{ number_format($item['amount'], 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #f7fafc; font-weight: bold;">
                <td>Subtotal</td>
                <td class="amount">Rp {{ number_format($payroll->base_salary + $payroll->bonus, 0, ',', '.') }}</td>
                <td class="amount">Rp {{ number_format($payroll->deductions, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="grand-total">
            Total Diterima (Take Home Pay): Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}
        </div>
        <div style="text-align: right; font-style: italic; font-size: 12px; margin-top: 5px;">
            (Terbilang:
            {{ \Illuminate\Support\Str::title(\App\Helpers\NumberHelper::terbilang($payroll->net_salary)) }}
            Rupiah)
        </div>
    </div>

    <div class="footer">
        <div class="sig-box">
            <div>Palembang, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Mengetahui,</div>
            <div class="sig-line">Direktur / Finance</div>
        </div>
        <div style="clear: both;"></div>
        <p>Dokumen ini diterbitkan secara otomatis oleh sistem SI-PEG Media Sriwijaya.</p>
    </div>
</body>

</html>