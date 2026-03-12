<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Summary Report PDF</title>
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
            max-width: 1000px;
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
        .total-revenue {
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
            border: 2px solid #940000;
            margin-bottom: 30px;
        }
        .total-revenue h2 {
            color: #940000;
            margin: 0;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .service-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .service-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #940000;
            padding-bottom: 5px;
        }
        .service-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ddd;
        }
        .service-row:last-child {
            border-bottom: none;
            font-weight: bold;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #940000;
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
            <p>Daily Summary Report</p>
            <p>Date: {{ $date->format('l, F d, Y') }}</p>
            <p>Generated: {{ now()->format('d M Y H:i:s') }}</p>
        </div>

        <div class="total-revenue">
            <h2>Total Revenue: TZS {{ number_format($summary['total_revenue'] ?? 0) }}</h2>
        </div>

        <div class="services-grid">
            <div class="service-card">
                <div class="service-title">Driving Range</div>
                <div class="service-row">
                    <span>Sessions:</span>
                    <span>{{ number_format($summary['driving_range']['sessions'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Completed:</span>
                    <span>{{ number_format($summary['driving_range']['completed'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Active:</span>
                    <span>{{ number_format($summary['driving_range']['active'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Revenue:</span>
                    <span>TZS {{ number_format($summary['driving_range']['revenue'] ?? 0) }}</span>
                </div>
            </div>

            <div class="service-card">
                <div class="service-title">Ball Management</div>
                <div class="service-row">
                    <span>Transactions:</span>
                    <span>{{ number_format($summary['ball_management']['transactions'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Revenue:</span>
                    <span>TZS {{ number_format($summary['ball_management']['revenue'] ?? 0) }}</span>
                </div>
            </div>

            <div class="service-card">
                <div class="service-title">Equipment Rental</div>
                <div class="service-row">
                    <span>Rentals:</span>
                    <span>{{ number_format($summary['equipment_rental']['rentals'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Returned:</span>
                    <span>{{ number_format($summary['equipment_rental']['returned'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Active:</span>
                    <span>{{ number_format($summary['equipment_rental']['active'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Revenue:</span>
                    <span>TZS {{ number_format($summary['equipment_rental']['revenue'] ?? 0) }}</span>
                </div>
            </div>

            <div class="service-card">
                <div class="service-title">Equipment Sales</div>
                <div class="service-row">
                    <span>Sales:</span>
                    <span>{{ number_format($summary['equipment_sales']['sales'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Completed:</span>
                    <span>{{ number_format($summary['equipment_sales']['completed'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Revenue:</span>
                    <span>TZS {{ number_format($summary['equipment_sales']['revenue'] ?? 0) }}</span>
                </div>
            </div>

            <div class="service-card">
                <div class="service-title">Food & Beverage</div>
                <div class="service-row">
                    <span>Orders:</span>
                    <span>{{ number_format($summary['food_beverage']['orders'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Completed:</span>
                    <span>{{ number_format($summary['food_beverage']['completed'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Pending:</span>
                    <span>{{ number_format($summary['food_beverage']['pending'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Revenue:</span>
                    <span>TZS {{ number_format($summary['food_beverage']['revenue'] ?? 0) }}</span>
                </div>
            </div>

            <div class="service-card">
                <div class="service-title">Top-ups</div>
                <div class="service-row">
                    <span>Count:</span>
                    <span>{{ number_format($summary['topups']['count'] ?? 0) }}</span>
                </div>
                <div class="service-row">
                    <span>Total Amount:</span>
                    <span>TZS {{ number_format($summary['topups']['amount'] ?? 0) }}</span>
                </div>
            </div>
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
                
                const filename = 'Daily_Summary_' + '{{ $date->format("Ymd") }}_' + new Date().getTime() + '.pdf';
                pdf.save(filename);
                
                setTimeout(function() {
                    document.body.innerHTML = '<div style="text-align:center;padding:50px;font-family:Arial;"><h2>PDF Downloaded Successfully!</h2><p>Daily Summary Report</p><button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#940000;color:white;border:none;cursor:pointer;">Close Window</button></div>';
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


