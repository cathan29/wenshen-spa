<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Ticket #{{ $queue->queue_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm; /* Standard width for thermal printers */
            margin: 0 auto;
            padding: 10px;
            background-color: white;
            color: black;
            text-align: center;
        }
        
        .header {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .queue-number {
            font-size: 42px;
            font-weight: 900;
            margin: 10px 0;
            line-height: 1;
        }

        .service-info {
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .meta-info {
            font-size: 10px;
            margin-bottom: 15px;
        }

        .qr-box {
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }

        .footer {
            font-size: 9px;
            margin-top: 10px;
            border-top: 1px dashed black;
            padding-top: 5px;
        }

        /* Hides the button when printing on paper */
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>
</head>
<body onload="window.print()"> {{-- Auto-trigger print dialog --}}

    <div class="header">
        WENSHEN SPA<br>
        <span style="font-size: 10px; font-weight: normal;">Queuing System</span>
    </div>

    {{-- The Big Number --}}
    <div class="queue-number">
        {{ $queue->queue_number }}
    </div>

    {{-- 👇 NOW SHOWS ALL TREATMENTS --}}
    <div class="service-info">
        {{ $queue->services->pluck('service_name')->join(', ') }}
    </div>

    {{-- 👇 NOW USES TOTAL PRICE --}}
    <div class="meta-info">
        Price: ₱{{ number_format($queue->total_price, 0) }}<br>
        Date: {{ $queue->created_at->format('M d, Y') }}<br>
        Time: {{ $queue->created_at->format('h:i A') }}
    </div>

    {{-- The Scan Code --}}
    <div class="qr-box">
        {!! QrCode::size(120)->generate(route('queue.show', $queue->qr_token)) !!}
    </div>

    <div class="footer">
        Scan to check your status<br>
        Please wait for your number.<br>
        ***
    </div>

    {{-- This button only shows on screen, not on paper --}}
    <button class="no-print" onclick="window.print()" style="margin-top:20px; width:100%; padding:10px; background:black; color:white; border:none; cursor:pointer;">
        🖨️ Print Ticket
    </button>

</body>
</html>