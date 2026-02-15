<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wenshen Spa Sales Report</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 2px solid #D4AF37; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 28px; font-weight: bold; color: #6B4E31; text-transform: uppercase; letter-spacing: 2px; }
        .subtitle { color: #777; font-size: 14px; margin-top: 5px; }
        .summary-box { background-color: #F9F3E3; padding: 15px; border-radius: 5px; margin-bottom: 30px; }
        .summary-box h3 { margin: 0 0 10px 0; color: #6B4E31; font-size: 16px; text-transform: uppercase; }
        .total-rev { font-size: 24px; font-weight: bold; color: #6B4E31; }
        table { w-full: 100%; border-collapse: collapse; margin-top: 20px; width: 100%; }
        th { background-color: #6B4E31; color: white; text-align: left; padding: 12px; font-size: 12px; text-transform: uppercase; }
        td { border-bottom: 1px solid #eee; padding: 12px; font-size: 14px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">Wenshen Beauty Spa</div>
        <div class="subtitle">Official Sales & Queue Report</div>
        <div class="subtitle"><strong>Period:</strong> {{ $period }}</div>
    </div>

    <div class="summary-box">
        <h3>Executive Summary</h3>
        <p style="margin:0;">Total Completed Services: <strong>{{ $totalClients }}</strong></p>
        <p style="margin:5px 0 0 0;">Total Revenue Generated: <span class="total-rev">Php {{ number_format($totalRevenue, 2) }}</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date / Time</th>
                <th>Client Name</th>
                <th>Treatment</th>
                <th class="text-right">Amount (Php)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allData as $data)
                <tr>
                    <td>{{ $data->updated_at->format('M d, Y h:i A') }}</td>
                    <td>{{ $data->customer_name }}</td>
                    <td>{{ $data->service ? $data->service->service_name : 'Deleted Service' }}</td>
                    <td class="text-right">{{ $data->service ? number_format($data->service->price, 2) : '0.00' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px; color:#999;">No completed transactions found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>