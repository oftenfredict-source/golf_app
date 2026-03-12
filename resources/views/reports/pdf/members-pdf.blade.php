<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Report PDF</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
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
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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
            <p>Member Report</p>
            <p>Generated: {{ now()->format('d M Y H:i:s') }}</p>
        </div>

        <div class="summary-cards">
            <div class="card">
                <div class="card-title">Total Members</div>
                <div class="card-value">{{ number_format($stats['total'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Active</div>
                <div class="card-value">{{ number_format($stats['active'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Expired</div>
                <div class="card-value">{{ number_format($stats['expired'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Suspended</div>
                <div class="card-value">{{ number_format($stats['suspended'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Total Balance</div>
                <div class="card-value">TZS {{ number_format($stats['total_balance'] ?? 0) }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Member ID</th>
                    <th>Name</th>
                    <th>Card Number</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-right">Balance</th>
                    <th class="text-right">Top-ups</th>
                    <th class="text-right">Transactions</th>
                    <th>Valid Until</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $member)
                <tr>
                    <td>{{ $member->member_id }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->card_number }}</td>
                    <td>{{ ucfirst($member->membership_type) }}</td>
                    <td>{{ ucfirst($member->status) }}</td>
                    <td class="text-right">TZS {{ number_format($member->balance) }}</td>
                    <td class="text-right">{{ number_format($member->topups_count ?? 0) }}</td>
                    <td class="text-right">{{ number_format($member->transactions_count ?? 0) }}</td>
                    <td>{{ $member->valid_until ? $member->valid_until->format('d M Y') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Generated by Golf Club Management System - EMCA Technologies</p>
            <p>Total Records: {{ $members->count() }}</p>
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
                
                const filename = 'Members_Report_' + new Date().getTime() + '.pdf';
                pdf.save(filename);
                
                setTimeout(function() {
                    document.body.innerHTML = '<div style="text-align:center;padding:50px;font-family:Arial;"><h2>PDF Downloaded Successfully!</h2><p>Member Report</p><button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#940000;color:white;border:none;cursor:pointer;">Close Window</button></div>';
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


