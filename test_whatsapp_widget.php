<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Widget Test - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .test-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
        }
        .test-header {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
        }
        .test-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #f9f9f9;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-success { background-color: #28a745; }
        .status-error { background-color: #dc3545; }
        .status-warning { background-color: #ffc107; }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .test-button {
            background: #25D366;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .test-button:hover {
            background: #128C7E;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .scroll-content {
            height: 200vh;
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            margin-top: 2rem;
            padding: 2rem;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1><i class="fab fa-whatsapp" style="color: #25D366;"></i> WhatsApp Widget Test</h1>
            <p class="lead">Testing the floating WhatsApp contact widget for Wishluv Buildcon</p>
        </div>

        <!-- Widget Status Check -->
        <div class="test-section">
            <h3><i class="fas fa-check-circle"></i> Widget Status</h3>
            <div id="widgetStatus">
                <p><span class="status-indicator status-warning"></span> Checking widget status...</p>
            </div>
        </div>

        <!-- Widget Features -->
        <div class="test-section">
            <h3><i class="fas fa-star"></i> Widget Features</h3>
            <ul class="feature-list">
                <li><i class="fas fa-check text-success"></i> Fixed position in bottom-right corner</li>
                <li><i class="fas fa-check text-success"></i> WhatsApp official green color (#25D366)</li>
                <li><i class="fas fa-check text-success"></i> Phone number: +91 7280008102</li>
                <li><i class="fas fa-check text-success"></i> Pre-filled message for better user experience</li>
                <li><i class="fas fa-check text-success"></i> Hover effects and animations</li>
                <li><i class="fas fa-check text-success"></i> Responsive design for mobile and desktop</li>
                <li><i class="fas fa-check text-success"></i> Pulse animation to attract attention</li>
                <li><i class="fas fa-check text-success"></i> Analytics tracking integration</li>
                <li><i class="fas fa-check text-success"></i> Excluded from admin pages</li>
            </ul>
        </div>

        <!-- Test Instructions -->
        <div class="test-section">
            <h3><i class="fas fa-clipboard-list"></i> Test Instructions</h3>
            <ol>
                <li><strong>Look for the floating WhatsApp button</strong> in the bottom-right corner of your screen</li>
                <li><strong>Check the button appearance:</strong> Green circular button with WhatsApp icon</li>
                <li><strong>Test hover effect:</strong> Hover over the button to see color change and scale effect</li>
                <li><strong>Test click functionality:</strong> Click the button to open WhatsApp with pre-filled message</li>
                <li><strong>Test responsiveness:</strong> Resize your browser window to see mobile adaptation</li>
                <li><strong>Test scroll behavior:</strong> Scroll down to see opacity changes near footer</li>
                <li><strong>Test on mobile:</strong> Open this page on a mobile device to verify mobile experience</li>
            </ol>
        </div>

        <!-- Direct Test Button -->
        <div class="test-section">
            <h3><i class="fas fa-rocket"></i> Direct Test</h3>
            <p>Click the button below to test the WhatsApp link directly:</p>
            <a href="https://wa.me/917280008102?text=Hi%2C%20I%27m%20interested%20in%20Wishluv%20Buildcon%20properties.%20Please%20provide%20more%20information." 
               class="test-button" 
               target="_blank" 
               rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i> Test WhatsApp Link
            </a>
        </div>

        <!-- Technical Details -->
        <div class="test-section">
            <h3><i class="fas fa-cog"></i> Technical Implementation</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>CSS Features:</h5>
                    <ul>
                        <li>Fixed positioning with high z-index (1000)</li>
                        <li>Smooth transitions and hover effects</li>
                        <li>Pulse animation using CSS keyframes</li>
                        <li>Responsive breakpoints for mobile devices</li>
                        <li>Box shadow for depth and visual appeal</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>JavaScript Features:</h5>
                    <ul>
                        <li>Analytics tracking (Google Analytics & Facebook Pixel)</li>
                        <li>Smooth entrance animation on page load</li>
                        <li>Scroll-based opacity adjustments</li>
                        <li>Event listeners for enhanced user experience</li>
                        <li>Error handling and fallbacks</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Browser Compatibility -->
        <div class="test-section">
            <h3><i class="fas fa-globe"></i> Browser Compatibility</h3>
            <div class="row">
                <div class="col-md-3">
                    <p><i class="fab fa-chrome text-success"></i> Chrome ✓</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fab fa-firefox text-success"></i> Firefox ✓</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fab fa-safari text-success"></i> Safari ✓</p>
                </div>
                <div class="col-md-3">
                    <p><i class="fab fa-edge text-success"></i> Edge ✓</p>
                </div>
            </div>
        </div>

        <!-- Scroll Test Area -->
        <div class="scroll-content">
            <h3><i class="fas fa-scroll"></i> Scroll Test Area</h3>
            <p>This is a long content area to test the scroll behavior of the WhatsApp widget.</p>
            <p>As you scroll down, you should notice the WhatsApp button remains fixed in position.</p>
            <p>Near the bottom of the page, the widget opacity may change slightly.</p>
            
            <div style="margin-top: 50vh;">
                <h4>Middle of scroll area</h4>
                <p>The WhatsApp widget should still be visible and functional here.</p>
            </div>
            
            <div style="margin-top: 50vh;">
                <h4>Bottom of scroll area</h4>
                <p>Test completed! The WhatsApp widget should maintain its functionality throughout the scroll.</p>
            </div>
        </div>
    </div>

    <?php include "vendor.php" ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if WhatsApp widget is present
        setTimeout(function() {
            const widget = document.querySelector('.whatsapp-float');
            const statusDiv = document.getElementById('widgetStatus');
            
            if (widget) {
                statusDiv.innerHTML = '<p><span class="status-indicator status-success"></span> WhatsApp widget loaded successfully!</p>';
                
                // Test widget properties
                const computedStyle = window.getComputedStyle(widget);
                const details = `
                    <div class="mt-3">
                        <small>
                            <strong>Position:</strong> ${computedStyle.position}<br>
                            <strong>Z-index:</strong> ${computedStyle.zIndex}<br>
                            <strong>Background:</strong> ${computedStyle.backgroundColor}<br>
                            <strong>Bottom:</strong> ${computedStyle.bottom}<br>
                            <strong>Right:</strong> ${computedStyle.right}
                        </small>
                    </div>
                `;
                statusDiv.innerHTML += details;
            } else {
                statusDiv.innerHTML = '<p><span class="status-indicator status-error"></span> WhatsApp widget not found! Please check the implementation.</p>';
            }
        }, 1500);
    });
    </script>
</body>
</html>
