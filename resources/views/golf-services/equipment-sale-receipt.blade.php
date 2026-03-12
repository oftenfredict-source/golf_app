<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Sale Receipt #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 0;
            margin: 0;
        }
        .receipt-container {
            max-width: 80mm;
            width: 80mm;
            margin: 0 auto;
            background: white;
            padding: 10mm 8mm;
            box-shadow: none;
            font-size: 12px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 3px solid #940000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .receipt-header h1 {
            color: #000;
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .receipt-header p {
            color: #000;
            font-size: 11px;
            margin: 2px 0;
        }
        .receipt-info {
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dotted #ccc;
            font-size: 11px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #000;
            font-weight: normal;
        }
        .info-value {
            color: #000;
            font-weight: bold;
            text-align: right;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        .items-table thead {
            background: #000;
            color: white;
        }
        .items-table th {
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
        }
        .items-table td {
            padding: 5px 4px;
            border-bottom: 1px dotted #ccc;
            font-size: 10px;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .items-table .item-name {
            max-width: 40%;
            word-wrap: break-word;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-section {
            background: #fff;
            padding: 10px 0;
            border-top: 2px dashed #000;
            border-bottom: 2px dashed #000;
            margin-bottom: 15px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 11px;
        }
        .summary-row.total {
            border-top: 2px solid #000;
            margin-top: 8px;
            padding-top: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }
        .footer {
            text-align: center;
            color: #000;
            font-size: 10px;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border: 1px solid #000;
            font-size: 9px;
            font-weight: bold;
            background: white;
            color: #000;
        }
        .badge-success {
            border-color: #000;
        }
        .badge-warning {
            border-color: #000;
        }
        .badge-danger {
            border-color: #000;
        }
        .notes-section {
            margin-bottom: 15px;
            padding: 8px 0;
            border-top: 1px dashed #000;
            font-size: 10px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .receipt-container {
                box-shadow: none;
                padding: 5mm;
                max-width: 80mm;
                width: 80mm;
            }
            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>GOLF CLUB</h1>
            <p>EQUIPMENT SALE RECEIPT</p>
            <p style="margin-top: 3px; font-size: 10px;">Receipt #{{ $sale->id }}</p>
            <p style="font-size: 9px; margin-top: 3px;">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">Sale ID:</span>
                <span class="info-value">#{{ $sale->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date & Time:</span>
                <span class="info-value">{{ $sale->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">{{ $sale->customer_name }}</span>
            </div>
            @if($sale->customer_upi)
            <div class="info-row">
                <span class="info-label">Card Number:</span>
                <span class="info-value">{{ $sale->customer_upi }}</span>
            </div>
            @endif
            @if($sale->customer_phone)
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $sale->customer_phone }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                <span class="info-value">
                    @if($sale->payment_method === 'balance')
                        <span class="badge badge-success">Member Balance</span>
                    @elseif($sale->payment_method === 'cash')
                        <span class="badge badge-success">Cash</span>
                    @elseif($sale->payment_method === 'card')
                        <span class="badge badge-success">Card</span>
                    @elseif($sale->payment_method === 'mobile' || $sale->payment_method === 'mobile_money')
                        <span class="badge badge-warning">Mobile Money</span>
                    @else
                        {{ ucfirst($sale->payment_method) }}
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    @if($sale->status === 'completed')
                        <span class="badge badge-success">Completed</span>
                    @elseif($sale->status === 'refunded')
                        <span class="badge badge-warning">Refunded</span>
                    @elseif($sale->status === 'cancelled')
                        <span class="badge badge-danger">Cancelled</span>
                    @else
                        {{ ucfirst($sale->status) }}
                    @endif
                </span>
            </div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">#</th>
                    <th class="item-name">Item</th>
                    <th class="text-center" style="width: 12%;">Qty</th>
                    <th class="text-right" style="width: 25%;">Price</th>
                    <th class="text-right" style="width: 25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="item-name">
                        {{ Str::limit($item->equipment->name ?? 'Unknown Product', 20) }}
                        @if($item->equipment && $item->equipment->sku)
                            <br><small>SKU:{{ $item->equipment->sku }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price) }}</td>
                    <td class="text-right"><strong>{{ number_format($item->subtotal) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-section">
            <div class="summary-row">
                <span>Subtotal:</span>
                <strong>{{ number_format($sale->subtotal) }}</strong>
            </div>
            @if($sale->discount > 0)
            <div class="summary-row">
                <span>Discount:</span>
                <strong>- {{ number_format($sale->discount) }}</strong>
            </div>
            @endif
            <div class="summary-row total">
                <span>TOTAL:</span>
                <span>TZS {{ number_format($sale->total_amount) }}</span>
            </div>
            @if($sale->sms_sent)
            <div class="summary-row" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                <span style="font-size: 12px; color: #666;">SMS Receipt:</span>
                <span class="badge badge-success" style="font-size: 11px;">Sent</span>
            </div>
            @endif
        </div>

        @if($sale->notes)
        <div class="notes-section">
            <strong>Notes:</strong><br>
            {{ $sale->notes }}
        </div>
        @endif

        <div class="footer">
            <p style="margin-bottom: 5px;">Thank you!</p>
            <p style="font-size: 9px;">Golf Club Management</p>
            <p style="font-size: 8px; margin-top: 3px;">EMCA Technologies</p>
        </div>
    </div>

    <!-- jsPDF library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <script>
        window.onload = function() {
            // Wait a bit for the page to fully render
            setTimeout(function() {
                generatePDF();
            }, 500);
        }

        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const element = document.querySelector('.receipt-container');
            
            if (!element) {
                console.error('Receipt container not found');
                return;
            }

            // Show loading message
            const loadingMsg = document.createElement('div');
            loadingMsg.id = 'pdfLoading';
            loadingMsg.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border:2px solid #333;z-index:9999;';
            loadingMsg.innerHTML = '<p>Generating PDF...</p>';
            document.body.appendChild(loadingMsg);

            html2canvas(element, {
                scale: 2,
                useCORS: true,
                logging: false,
                width: element.scrollWidth,
                height: element.scrollHeight,
                windowWidth: element.scrollWidth,
                windowHeight: element.scrollHeight
            }).then(function(canvas) {
                // Remove loading message
                const loadingEl = document.getElementById('pdfLoading');
                if (loadingEl) loadingEl.remove();

                const imgData = canvas.toDataURL('image/png');
                
                // Calculate dimensions for 80mm width (in mm)
                const pdfWidth = 80; // 80mm for thermal printer
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                
                const pdf = new jsPDF('p', 'mm', [pdfWidth, pdfHeight]);
                
                // Add image to PDF
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                
                // Generate filename
                const filename = 'Equipment_Sale_Receipt_' + {{ $sale->id }} + '_' + new Date().getTime() + '.pdf';
                
                // Save PDF
                pdf.save(filename);
                
                // Optionally redirect back or show message after download
                setTimeout(function() {
                    // You can optionally close the window or redirect
                    // window.close();
                    // Or show a message
                    document.body.innerHTML = '<div style="text-align:center;padding:50px;font-family:Arial;"><h2>PDF Downloaded Successfully!</h2><p>Receipt #{{ $sale->id }}</p><button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#940000;color:white;border:none;cursor:pointer;">Close Window</button></div>';
                }, 1000);
            }).catch(function(error) {
                console.error('Error generating PDF:', error);
                const loadingEl = document.getElementById('pdfLoading');
                if (loadingEl) {
                    loadingEl.innerHTML = '<p style="color:red;">Error generating PDF. Please try printing instead.</p>';
                    setTimeout(function() {
                        loadingEl.remove();
                    }, 3000);
                }
            });
        }
    </script>
</body>
</html>

