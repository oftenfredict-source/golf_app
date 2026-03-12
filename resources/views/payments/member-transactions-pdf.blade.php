<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - {{ $member->name }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            color: black;
        }
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #940000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #940000;
            margin: 0;
            font-size: 24px;
        }
        .member-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #940000;
        }
        .member-info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ddd;
        }
        .member-info-row:last-child {
            border-bottom: none;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .card-title {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 16px;
            font-weight: bold;
            color: #940000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #940000;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="report-container" id="reportContent">
        <div class="header">
            <h1>Golf Club Management System</h1>
            <p>Transaction History Report</p>
            <p>Generated: {{ now()->format('d M Y H:i:s') }}</p>
        </div>

        <div class="member-info">
            <div class="member-info-row">
                <span><strong>Member Name:</strong></span>
                <span><strong>{{ $member->name }}</strong></span>
            </div>
            <div class="member-info-row">
                <span>Card Number:</span>
                <span><code>{{ $member->card_number }}</code></span>
            </div>
            <div class="member-info-row">
                <span>Member ID:</span>
                <span>{{ $member->member_id }}</span>
            </div>
            <div class="member-info-row">
                <span>Phone:</span>
                <span>{{ $member->phone }}</span>
            </div>
            <div class="member-info-row">
                <span>Email:</span>
                <span>{{ $member->email ?? 'N/A' }}</span>
            </div>
            <div class="member-info-row">
                <span>Membership Type:</span>
                <span>{{ ucfirst($member->membership_type) }}</span>
            </div>
            <div class="member-info-row">
                <span>Status:</span>
                <span>{{ ucfirst($member->status) }}</span>
            </div>
            <div class="member-info-row">
                <span><strong>Current Balance:</strong></span>
                <span><strong class="text-primary">TZS {{ number_format($member->balance, 2) }}</strong></span>
            </div>
        </div>

        <div class="summary-cards">
            <div class="card">
                <div class="card-title">Total Transactions</div>
                <div class="card-value">{{ number_format($summary['total_transactions'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Total Payments</div>
                <div class="card-value">TZS {{ number_format($summary['total_payments'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Total Top-ups</div>
                <div class="card-value">TZS {{ number_format($summary['total_topups'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Total Refunds</div>
                <div class="card-value">TZS {{ number_format($summary['total_refunds'] ?? 0) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Balance Before</th>
                    <th class="text-right">Balance After</th>
                    <th>Payment Method</th>
                    <th>Date & Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr>
                    <td><code>{{ $txn->transaction_id }}</code></td>
                    <td>
                        @if($txn->type === 'payment')
                            Payment
                        @elseif($txn->type === 'topup')
                            Top-up
                        @elseif($txn->type === 'refund')
                            Refund
                        @else
                            {{ ucfirst($txn->type) }}
                        @endif
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $txn->category)) }}</td>
                    <td class="text-right">
                        <strong class="{{ $txn->type === 'payment' ? 'text-danger' : ($txn->type === 'topup' ? 'text-success' : 'text-warning') }}">
                            {{ $txn->type === 'payment' ? '-' : '+' }}TZS {{ number_format($txn->amount, 2) }}
                        </strong>
                    </td>
                    <td class="text-right">TZS {{ number_format($txn->balance_before ?? 0, 2) }}</td>
                    <td class="text-right"><strong>TZS {{ number_format($txn->balance_after ?? 0, 2) }}</strong></td>
                    <td>{{ strtoupper($txn->payment_method) }}</td>
                    <td>{{ $txn->created_at->format('d M Y H:i') }}</td>
                    <td>{{ ucfirst($txn->status) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4">No transactions found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Generated by Golf Club Management System - EMCA Technologies</p>
            <p>Total Records: {{ $transactions->count() }}</p>
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
            const element = document.getElementById('reportContent');
            
            if (!element) {
                console.error('Report content not found');
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
                const pdfWidth = 210; // A4 width in mm
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
                
                const pdf = new jsPDF('l', 'mm', [297, pdfHeight]); // Landscape for wide tables
                pdf.addImage(imgData, 'PNG', 0, 0, 297, pdfHeight);
                
                const filename = 'TransactionHistory_' + '{{ $member->name }}'.replace(/[^a-zA-Z0-9]/g, '_') + '_' + '{{ $member->card_number }}' + '_' + new Date().getTime() + '.pdf';
                pdf.save(filename);
                
                setTimeout(function() {
                    document.body.innerHTML = '<div style="text-align:center;padding:50px;font-family:Arial;"><h2>PDF Downloaded Successfully!</h2><p>Transaction History for {{ $member->name }}</p><button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#940000;color:white;border:none;cursor:pointer;">Close Window</button></div>';
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


