<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Report PDF</title>
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
            max-width: 800px;
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
        .header p {
            color: #666;
            margin: 5px 0;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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
            font-size: 18px;
            font-weight: bold;
            color: #940000;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #940000;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
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
            <p>Revenue Report</p>
            <p>Period: {{ $fromDate->format('d M Y') }} to {{ $toDate->format('d M Y') }}</p>
            <p>Generated: {{ now()->format('d M Y H:i:s') }}</p>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="card">
                <div class="card-title">Driving Range</div>
                <div class="card-value">TZS {{ number_format($revenueByCategory['driving_range'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Equipment Rental</div>
                <div class="card-value">TZS {{ number_format($revenueByCategory['equipment_rental'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Equipment Sales</div>
                <div class="card-value">TZS {{ number_format($revenueByCategory['equipment_sales'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Food & Beverage</div>
                <div class="card-value">TZS {{ number_format($revenueByCategory['food_beverage'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Ball Management</div>
                <div class="card-value">TZS {{ number_format($revenueByCategory['ball_management'] ?? 0) }}</div>
            </div>
            <div class="card">
                <div class="card-title">Total Revenue</div>
                <div class="card-value">TZS {{ number_format($revenueByCategory['total'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Revenue by Category -->
        <div class="section">
            <div class="section-title">Revenue by Category</div>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th class="text-right">Amount (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Driving Range</td>
                        <td class="text-right">{{ number_format($revenueByCategory['driving_range'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Ball Management</td>
                        <td class="text-right">{{ number_format($revenueByCategory['ball_management'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Equipment Rental</td>
                        <td class="text-right">{{ number_format($revenueByCategory['equipment_rental'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Equipment Sales</td>
                        <td class="text-right">{{ number_format($revenueByCategory['equipment_sales'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Food & Beverage</td>
                        <td class="text-right">{{ number_format($revenueByCategory['food_beverage'] ?? 0) }}</td>
                    </tr>
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td>Total Revenue</td>
                        <td class="text-right">TZS {{ number_format($revenueByCategory['total'] ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="section">
            <div class="section-title">Payment Method Breakdown</div>
            <table>
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th class="text-right">Amount (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Member Balance</td>
                        <td class="text-right">{{ number_format($paymentMethodBreakdown['balance'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Cash</td>
                        <td class="text-right">{{ number_format($paymentMethodBreakdown['cash'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Card</td>
                        <td class="text-right">{{ number_format($paymentMethodBreakdown['card'] ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>Mobile Money</td>
                        <td class="text-right">{{ number_format($paymentMethodBreakdown['mobile'] ?? 0) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="footer">
            <p>Generated by Golf Club Management System - EMCA Technologies</p>
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
                
                const pdf = new jsPDF('p', 'mm', [pdfWidth, pdfHeight]);
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                
                const filename = 'Revenue_Report_' + '{{ $fromDate->format("Ymd") }}_' + '{{ $toDate->format("Ymd") }}_' + new Date().getTime() + '.pdf';
                pdf.save(filename);
                
                setTimeout(function() {
                    document.body.innerHTML = '<div style="text-align:center;padding:50px;font-family:Arial;"><h2>PDF Downloaded Successfully!</h2><p>Revenue Report</p><button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#940000;color:white;border:none;cursor:pointer;">Close Window</button></div>';
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


