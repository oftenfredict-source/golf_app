<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Identity Card - {{ $member->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        // Fallback QR code generation if library fails
        if (typeof QRCode === 'undefined') {
            console.warn('QRCode library not loaded, will use fallback');
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        // Store data from Blade template safely
        const memberData = {
            name: @json($member->name),
            memberId: @json($member->member_id),
            cardNumber: @json($member->card_number),
            validUntil: @json($member->valid_until ? \Carbon\Carbon::parse($member->valid_until)->format('d M Y') : 'N/A'),
            balance: @json('TZS ' . number_format($member->balance, 0)),
            issueDate: @json($member->created_at->format('d M Y')),
            phone: @json($member->phone),
            email: @json($member->email ?? ''),
            upiUrl: @json($upiUrl)
        };
        
        // Track if QR code has been generated to prevent duplicates
        let qrCodeGenerated = false;
        
        // Generate QR Code on page load
        function generateQRCode() {
            // Prevent multiple generations
            if (qrCodeGenerated) {
                return;
            }
            
            const qrContainer = document.getElementById('qrcode');
            if (!qrContainer) return;
            
            // Check if QR code already exists and clean up duplicates
            const existingCanvas = qrContainer.querySelector('canvas');
            const existingImg = qrContainer.querySelector('img');
            if (existingCanvas || existingImg) {
                // If both exist, remove img and keep canvas
                if (existingCanvas && existingImg) {
                    existingImg.remove();
                }
                qrCodeGenerated = true;
                return;
            }
            
            // Clear container completely - remove all children including text nodes
            qrContainer.innerHTML = '';
            
            if (typeof QRCode !== 'undefined') {
                try {
                    // Create QR code instance - this will add canvas/img to container
                    // Use smaller size to ensure quiet zone (whitespace) around QR code for scanning
                    const qrInstance = new QRCode(qrContainer, {
                        text: memberData.upiUrl,
                        width: 70,
                        height: 70,
                        colorDark: '#000000',
                        colorLight: '#FFFFFF',
                        correctLevel: QRCode.CorrectLevel.H,
                        margin: 2  // Add margin for quiet zone
                    });
                    
                    qrCodeGenerated = true;
                    
                    // Style the QR code once rendered
                    setTimeout(function() {
                        // Remove any text nodes that might have been added
                        const children = Array.from(qrContainer.childNodes);
                        children.forEach(function(child) {
                            if (child.nodeType === Node.TEXT_NODE) {
                                qrContainer.removeChild(child);
                            }
                        });
                        
                        const canvas = qrContainer.querySelector('canvas');
                        const img = qrContainer.querySelector('img');
                        
                        // Remove duplicates - if both exist, keep only canvas (preferred)
                        if (canvas && img) {
                            img.remove(); // Remove img, keep canvas
                        }
                        
                        // Style the remaining element
                        if (canvas) {
                            canvas.style.width = '70px';
                            canvas.style.height = '70px';
                            canvas.style.maxWidth = '70px';
                            canvas.style.maxHeight = '70px';
                            canvas.style.display = 'block';
                            canvas.style.margin = '0 auto';
                            // Hide img if it still exists
                            if (img) img.style.display = 'none';
                        } else if (img) {
                            img.style.width = '70px';
                            img.style.height = '70px';
                            img.style.maxWidth = '70px';
                            img.style.maxHeight = '70px';
                            img.style.display = 'block';
                            img.style.margin = '0 auto';
                        }
                    }, 300);
                } catch (e) {
                    console.error('QR Code generation error:', e);
                    qrContainer.innerHTML = '<div style="width:70px;height:70px;background:#f0f0f0;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:7px;color:#999;">QR<br/>CODE</div>';
                    qrCodeGenerated = true;
                }
            } else {
                qrContainer.innerHTML = '<div style="width:76px;height:76px;background:#f0f0f0;border:1px solid #ccc;display:flex;align-items:center;justify-content:center;font-size:7px;color:#999;">QR<br/>CODE</div>';
                qrCodeGenerated = true;
            }
        }
        
        // Clean, working PDF download function
        async function downloadMemberCard() {
            const { jsPDF } = window.jspdf;
            
            const frontPage = document.getElementById('cardFrontPage');
            const backPage = document.getElementById('cardBackPage');
            const loadingEl = document.getElementById('pdfLoading');
            
            if (!frontPage || !backPage) {
                alert('Card elements missing!');
                return;
            }
            
            // Show loading
            if (!loadingEl) {
                const loadingMsg = document.createElement('div');
                loadingMsg.id = 'pdfLoading';
                loadingMsg.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:20px;border:2px solid #333;z-index:9999;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.3);';
                loadingMsg.innerHTML = '<p style="margin:0;font-size:16px;">Generating PDF...</p><p style="margin:5px 0 0 0;font-size:12px;color:#666;">Please wait...</p>';
                document.body.appendChild(loadingMsg);
            }
            
            const cardWidth = 85.6;
            const cardHeight = 53.98;
            
            try {
                // Ensure QR code is rendered
                await new Promise(resolve => setTimeout(resolve, 800));
                
                const frontCanvas = await html2canvas(frontPage, {
                    scale: 4,
                    backgroundColor: '#ffffff',
                    useCORS: true
                });
                
                const backCanvas = await html2canvas(backPage, {
                    scale: 4,
                    backgroundColor: '#ffffff',
                    useCORS: true,
                    onclone: doc => {
                        const qr = doc.getElementById('qrcode');
                        if (qr) {
                            qr.style.display = 'block';
                            qr.style.visibility = 'visible';
                        }
                    }
                });
                
                const pdf = new jsPDF({
                    orientation: 'landscape',
                    unit: 'mm',
                    format: [cardWidth, cardHeight]
                });
                
                pdf.addImage(
                    frontCanvas.toDataURL('image/png'),
                    'PNG',
                    0,
                    0,
                    cardWidth,
                    cardHeight,
                    undefined,
                    'FAST'
                );
                
                pdf.addPage([cardWidth, cardHeight], 'landscape');
                
                pdf.addImage(
                    backCanvas.toDataURL('image/png'),
                    'PNG',
                    0,
                    0,
                    cardWidth,
                    cardHeight,
                    undefined,
                    'FAST'
                );
                
                const safeName = memberData.name.replace(/[^a-z0-9]/gi, '_');
                
                pdf.save(`MemberCard_${safeName}_${memberData.cardNumber}.pdf`);
                
                const loadingElement = document.getElementById('pdfLoading');
                if (loadingElement) loadingElement.remove();
                
                showSuccessPopup(memberData.name);
            } catch (error) {
                console.error(error);
                const loadingElement = document.getElementById('pdfLoading');
                if (loadingElement) {
                    loadingElement.innerHTML = '<p style="color:red;margin:0;">PDF generation failed.</p>';
                    setTimeout(() => loadingElement.remove(), 3000);
                }
                alert('Error generating PDF: ' + error.message);
            }
        }
        
        function showSuccessPopup(memberName) {
            const popup = document.createElement('div');
            popup.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:25px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.3);z-index:10000;text-align:center;min-width:300px;';
            
            const memberNameEscaped = memberName.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            
            popup.innerHTML = '<div style="font-size:40px;color:green;margin-bottom:10px;">✓</div><h3 style="color:#940000;margin:0 0 10px 0;">PDF Downloaded Successfully</h3><p style="color:#666;margin:10px 0;"><strong>Member:</strong> ' + memberNameEscaped + '</p><p style="color:#999;font-size:12px;margin:5px 0;">Card Size: 85.6mm × 53.98mm</p><button onclick="this.parentElement.remove()" style="margin-top:15px;padding:10px 25px;background:#940000;color:white;border:none;cursor:pointer;border-radius:6px;font-size:14px;font-weight:600;">OK</button>';
            
            document.body.appendChild(popup);
            
            setTimeout(() => {
                if (popup.parentElement) {
                    popup.remove();
                }
            }, 5000);
        }
        
        // Make function available globally
        window.downloadMemberCard = downloadMemberCard;
        
        // Generate QR code on page load - only once
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                generateQRCode();
            });
        } else {
            // DOM already loaded
            generateQRCode();
        }
    </script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            gap: 10px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .download-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .download-btn:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        /* Print styles removed - PDF download only */
        /* Card dimensions: Standard ID card size 85.60mm × 53.98mm */
        .card-page {
            width: 85.60mm;
            height: 53.98mm;
            margin: 10mm;
            page-break-after: always;
        }
        .card-page {
            display: inline-block;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 8px;
            overflow: hidden;
        }
        .member-card {
            width: 100%;
            height: 100%;
            position: relative;
        }
        .card-front {
            width: 100%;
            height: 100%;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .card-back {
            width: 100%;
            height: 100%;
            padding: 4mm;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2mm;
        }
        .card-header h3 {
            font-size: 8mm;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
        }
        .card-header .subtitle {
            font-size: 3.5mm;
            opacity: 0.9;
        }
        .membership-badge {
            padding: 2mm 4mm;
            border-radius: 4mm;
            font-size: 3.5mm;
            font-weight: 600;
            text-align: center;
        }
        .member-info-section {
            display: flex;
            gap: 3mm;
            flex: 1;
        }
        .photo-placeholder {
            width: 20mm;
            height: 25mm;
            background: rgba(255,255,255,0.2);
            border-radius: 2mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-placeholder i {
            font-size: 12mm;
        }
        .member-details {
            flex: 1;
        }
        .detail-item {
            margin-bottom: 1.5mm;
        }
        .detail-label {
            font-size: 2.5mm;
            opacity: 0.8;
            margin-bottom: 0.5mm;
        }
        .detail-value {
            font-size: 4mm;
            font-weight: 600;
        }
        .member-id-value {
            font-size: 5mm;
            font-weight: 700;
        }
        .card-number-box {
            padding: 2mm;
            border-radius: 2mm;
            text-align: center;
            margin-top: auto;
        }
        .card-number-label {
            font-size: 2.5mm;
            opacity: 0.8;
            margin-bottom: 1mm;
        }
        .card-number-value {
            font-size: 5mm;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .card-back-header {
            text-align: center;
            margin-bottom: 3mm;
            font-size: 6mm;
            font-weight: 700;
        }
        .back-details {
            display: flex;
            justify-content: space-between;
            flex: 1;
        }
        .back-left {
            flex: 1;
        }
        .back-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .back-detail-item {
            margin-bottom: 2mm;
        }
        .back-label {
            font-size: 2.5mm;
            margin-bottom: 0.5mm;
        }
        .back-value {
            font-size: 3.5mm;
            font-weight: 600;
        }
        .back-member-id {
            font-size: 4.5mm;
            font-weight: 700;
        }
        .helpline-box {
            margin-top: 3mm;
            padding: 2mm;
            border-radius: 2mm;
            font-size: 2.5mm;
            text-align: center;
        }
        .qrcode-container {
            width: 100px;
            height: 100px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2mm;
            position: relative;
            padding: 12px;
            box-sizing: border-box;
        }
        
        /* Ensure only canvas or img is visible in QR container */
        /* QR code should be smaller than container to provide quiet zone (whitespace) for scanning */
        /* Quiet zone should be at least 4 modules (units) around QR code - approximately 10-15% of QR size */
        /* Container: 100px, Padding: 12px each side (24px total), QR: 70px = ~3px whitespace per side + library margin */
        .qrcode-container canvas,
        .qrcode-container img {
            max-width: 70px !important;
            max-height: 70px !important;
            width: 70px !important;
            height: 70px !important;
            display: block !important;
            margin: 0 auto !important;
        }
        
        /* Hide any text nodes or unwanted content - only show canvas/img */
        .qrcode-container > *:not(canvas):not(img) {
            display: none !important;
        }
        
        /* Prevent duplication - if both canvas and img exist, hide img and show only canvas */
        /* Use sibling selector to hide img when canvas exists */
        .qrcode-container canvas ~ img {
            display: none !important;
        }
        /* Also hide img if it comes before canvas */
        .qrcode-container:has(canvas) img {
            display: none !important;
        }
        
        .qrcode-label {
            font-size: 2.5mm;
            text-align: center;
        }
        .terms-section {
            margin-top: auto;
            font-size: 2mm;
            text-align: center;
            padding-top: 2mm;
        }
        .decorative-icon {
            position: absolute;
            top: 2mm;
            right: 2mm;
            opacity: 0.1;
        }
        .decorative-icon i {
            font-size: 20mm;
        }
        .card-preview-container {
            margin: 20px 0;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .info-banner {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            text-align: center;
        }
        
        .info-banner h2 {
            color: #940000;
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        
        .info-banner p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <!-- Download Controls -->
    <div class="print-controls">
        <button class="download-btn" onclick="downloadMemberCard()">
            <i class="ri-download-line"></i> Download PDF
        </button>
    </div>
    
    <!-- Info Banner -->
    <div class="info-banner">
        <h2>Member Identity Card</h2>
        <p><strong>{{ $member->name }}</strong> - Card #{{ $member->card_number }}</p>
        <p style="font-size: 12px; color: #999;">Card Size: 85.6mm × 53.98mm (Standard ID Card) | Click Download PDF to save</p>
    </div>
    
    <!-- Card Preview Container -->
    <div class="card-preview-container">
    @php
        $cardColor = $member->card_color ?? 'silver';
        if ($cardColor === 'silver') {
            $bgGradient = 'linear-gradient(135deg, #e0e0e0 0%, #bdbdbd 50%, #e0e0e0 100%)';
            $textColor = '#2c3e50';
        } elseif ($cardColor === 'black') {
            $bgGradient = 'linear-gradient(135deg, #2c3e50 0%, #000000 50%, #2c3e50 100%)';
            $textColor = '#ffffff';
        } elseif ($cardColor === 'gold') {
            $bgGradient = 'linear-gradient(135deg, #ffd700 0%, #ffa500 50%, #ffd700 100%)';
            $textColor = '#5d4037';
        } else {
            $bgGradient = 'linear-gradient(135deg, #940000 0%, #5d0000 50%, #940000 100%)';
            $textColor = '#ffffff';
        }
    @endphp
    
    <!-- Front of Card - Page 1 -->
    <div class="card-page" id="cardFrontPage">
        <div class="card-container">
            <div class="member-card">
                <div class="card-front" id="cardFront" style="background: {{ $bgGradient }}; color: {{ $textColor }};">
                    <div class="decorative-icon">
                        <i class="icon-base ri ri-golf-ball-line"></i>
                    </div>
                    
                    <div class="card-header">
                        <div>
                            <h3 style="color: {{ $textColor }};">GOLF CLUB</h3>
                            <div class="subtitle" style="color: {{ $textColor }}; opacity: 0.9;">MEMBER IDENTITY CARD</div>
                        </div>
                        <span class="membership-badge" style="background: {{ $member->card_color === 'silver' ? 'rgba(0,0,0,0.2)' : ($member->card_color === 'black' ? 'rgba(255,255,255,0.2)' : ($member->card_color === 'gold' ? 'rgba(0,0,0,0.2)' : 'rgba(255,255,255,0.2)')) }}; color: {{ $textColor }};">
                            {{ strtoupper($member->membership_type ?? 'STANDARD') }}
                        </span>
                    </div>
                    
                    <div class="member-info-section">
                        <div class="photo-placeholder">
                            <i class="icon-base ri ri-user-line"></i>
                        </div>
                        <div class="member-details">
                            <div class="detail-item">
                                <span class="detail-label" style="color: {{ $textColor }}; opacity: 0.8;">MEMBER NAME</span>
                                <div class="detail-value" style="color: {{ $textColor }};">{{ strtoupper($member->name) }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label" style="color: {{ $textColor }}; opacity: 0.8;">MEMBER ID</span>
                                <div class="detail-value member-id-value" style="color: {{ $textColor }};">{{ $member->member_id }}</div>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label" style="color: {{ $textColor }}; opacity: 0.8;">VALID UNTIL</span>
                                <div class="detail-value" style="color: {{ $textColor }};">{{ $member->valid_until ? \Carbon\Carbon::parse($member->valid_until)->format('d M Y') : 'N/A' }}</div>
                            </div>
                            @if($member->show_balance ?? true)
                            <div class="detail-item">
                                <span class="detail-label" style="color: {{ $textColor }}; opacity: 0.8;">BALANCE</span>
                                <div class="detail-value" style="color: {{ $textColor === '#ffffff' ? '#4CAF50' : '#2E7D32' }}; font-weight: 700;">TZS {{ number_format($member->balance, 0) }}</div>
                            </div>
                            @endif
                            @if($member->ball_limit)
                            <div class="detail-item">
                                <span class="detail-label" style="color: {{ $textColor }}; opacity: 0.8;">BALL LIMIT</span>
                                <div class="detail-value" style="color: {{ $textColor }};">{{ $member->ball_limit }} balls</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-number-box" style="background: {{ $member->card_color === 'silver' ? 'rgba(0,0,0,0.15)' : ($member->card_color === 'black' ? 'rgba(255,255,255,0.15)' : ($member->card_color === 'gold' ? 'rgba(0,0,0,0.15)' : 'rgba(0,0,0,0.2)')) }};">
                        <span class="card-number-label" style="color: {{ $textColor }}; opacity: 0.8;">CARD NUMBER</span>
                        <div class="card-number-value" style="color: {{ $textColor }};">{{ $member->card_number }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back of Card - Page 2 -->
    <div class="card-page" id="cardBackPage">
        <div class="card-container">
            <div class="member-card">
                <div class="card-back" id="cardBack" style="background: {{ $bgGradient }}; color: {{ $textColor }};">
                    <div class="decorative-icon">
                        <i class="icon-base ri ri-golf-ball-line"></i>
                    </div>
                    <div class="back-details">
                        <div class="back-left">
                            <div class="back-detail-item">
                                <span class="back-label" style="color: {{ $textColor }}; opacity: 0.8;">MEMBER ID</span>
                                <div class="back-value back-member-id" style="color: {{ $textColor }};">{{ $member->member_id }}</div>
                            </div>
                            <div class="back-detail-item">
                                <span class="back-label" style="color: {{ $textColor }}; opacity: 0.8;">ISSUE DATE</span>
                                <div class="back-value" style="color: {{ $textColor }};">{{ $member->created_at->format('d M Y') }}</div>
                            </div>
                            <div class="back-detail-item">
                                <span class="back-label" style="color: {{ $textColor }}; opacity: 0.8;">PHONE</span>
                                <div class="back-value" style="color: {{ $textColor }};">{{ $member->phone }}</div>
                            </div>
                            @if($member->email)
                            <div class="back-detail-item">
                                <span class="back-label" style="color: {{ $textColor }}; opacity: 0.8;">EMAIL</span>
                                <div class="back-value" style="font-size: 11px; color: {{ $textColor }};">{{ $member->email }}</div>
                            </div>
                            @endif
                            <div class="helpline-box" style="background: {{ $member->card_color === 'silver' ? 'rgba(0,0,0,0.15)' : ($member->card_color === 'black' ? 'rgba(255,255,255,0.15)' : ($member->card_color === 'gold' ? 'rgba(0,0,0,0.15)' : 'rgba(255,255,255,0.15)')) }}; color: {{ $textColor }};">
                                <i class="icon-base ri ri-phone-line"></i> Helpline: +255 XXX XXX XXX
                            </div>
                        </div>
                        <div class="back-right">
                            <div class="qrcode-container" id="qrcode" style="background: white; border: 2px solid {{ $textColor === '#ffffff' ? 'rgba(255,255,255,0.5)' : 'rgba(0,0,0,0.3)' }}; padding: 12px;"></div>
                            <span class="qrcode-label" style="color: {{ $textColor }};">Scan for Payment</span>
                        </div>
                    </div>
                    
                    <div class="terms-section" style="color: {{ $textColor }}; opacity: 0.9; border-top: 1px solid {{ $textColor === '#ffffff' ? 'rgba(255,255,255,0.3)' : 'rgba(0,0,0,0.2)' }};">
                        This card is property of Golf Club. If found, please return to the nearest Golf Club office.
                        For lost/stolen cards, call helpline immediately.
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
