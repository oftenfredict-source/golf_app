<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt #{{ $transaction->transaction_id }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
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
    </style>
</head>
<body>
    <div class="receipt-container" id="receiptContent">
        <div class="receipt-header">
            <h1>GOLF CLUB</h1>
            <p>TRANSACTION RECEIPT</p>
            <p style="margin-top: 3px; font-size: 10px;">{{ $transaction->transaction_id }}</p>
            <p style="font-size: 9px; margin-top: 3px;">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">Transaction ID:</span>
                <span class="info-value">{{ $transaction->transaction_id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">{{ $transaction->customer_name }}</span>
            </div>
            @if($transaction->member && $transaction->member->card_number)
            <div class="info-row">
                <span class="info-label">Card Number:</span>
                <span class="info-value">{{ $transaction->member->card_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Date & Time:</span>
                <span class="info-value">{{ $transaction->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Type:</span>
                <span class="info-value">{{ ucfirst($transaction->type) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Category:</span>
                <span class="info-value">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                <span class="info-value">{{ strtoupper($transaction->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ ucfirst($transaction->status) }}</span>
            </div>
            @if($transaction->notes)
            <div class="info-row">
                <span class="info-label">Notes:</span>
                <span class="info-value" style="font-size: 10px;">{{ $transaction->notes }}</span>
            </div>
            @endif
        </div>

        @if($transaction->member_id)
        <div class="summary-section">
            <div class="summary-row">
                <span>Balance Before:</span>
                <strong>TZS {{ number_format($transaction->balance_before) }}</strong>
            </div>
            <div class="summary-row">
                <span>Transaction Amount:</span>
                <strong>{{ $transaction->type === 'payment' ? '-' : '+' }}TZS {{ number_format($transaction->amount) }}</strong>
            </div>
            <div class="summary-row total">
                <span>Balance After:</span>
                <span>TZS {{ number_format($transaction->balance_after) }}</span>
            </div>
        </div>
        @else
        <div class="summary-section">
            <div class="summary-row total">
                <span>Amount:</span>
                <span>{{ $transaction->type === 'payment' ? '-' : '+' }}TZS {{ number_format($transaction->amount) }}</span>
            </div>
        </div>
        @endif

        <div class="footer">
            <p style="margin-bottom: 5px;">Thank you!</p>
            <p style="font-size: 9px;">Golf Club Management</p>
            <p style="font-size: 8px; margin-top: 3px;">EMCA Technologies</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function() {
                generatePDF();
            }, 500);
        }

        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const element = document.getElementById('receiptContent');
            
            if (!element) {
                console.error('Receipt content not found');
                return;
            }

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
                const loadingEl = document.getElementById('pdfLoading');
                if (loadingEl) loadingEl.remove();

                const imgData = canvas.toDataURL('image/png');
                const pdfWidth = 80; // 80mm for thermal printer
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                
                const pdf = new jsPDF('p', 'mm', [pdfWidth, pdfHeight]);
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                
                const filename = 'TransactionReceipt_' + '{{ $transaction->transaction_id }}' + '_' + new Date().getTime() + '.pdf';
                pdf.save(filename);
                
                setTimeout(function() {
                    document.body.innerHTML = '<div style="text-align:center;padding:50px;font-family:Arial;"><h2>PDF Downloaded Successfully!</h2><p>Transaction #{{ $transaction->transaction_id }}</p><button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#940000;color:white;border:none;cursor:pointer;">Close Window</button></div>';
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


