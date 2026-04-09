<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favicon Test - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .test-container {
            max-width: 900px;
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
        .favicon-preview {
            display: inline-block;
            margin: 0.5rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .favicon-preview img {
            display: block;
            margin: 0 auto 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .favicon-size {
            font-size: 0.8rem;
            color: #666;
            font-weight: bold;
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
        .code-block {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }
        .browser-tab-demo {
            background: #f0f0f0;
            border-radius: 8px 8px 0 0;
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-bottom: none;
            display: inline-block;
            margin: 1rem 0;
        }
        .browser-content {
            background: white;
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 0 8px 8px 8px;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1><i class="fas fa-star" style="color: #4f8f70;"></i> Favicon Implementation Test</h1>
            <p class="lead">Testing the Wishluv Buildcon company logo as favicon</p>
        </div>

        <!-- Favicon Status -->
        <div class="test-section">
            <h3><i class="fas fa-check-circle"></i> Favicon Implementation Status</h3>
            <ul class="feature-list">
                <li><i class="fas fa-check text-success"></i> Company logo converted to multiple favicon sizes</li>
                <li><i class="fas fa-check text-success"></i> Standard favicon.ico created (16x16, 32x32)</li>
                <li><i class="fas fa-check text-success"></i> High-resolution PNG favicons generated</li>
                <li><i class="fas fa-check text-success"></i> Apple Touch Icons for iOS devices</li>
                <li><i class="fas fa-check text-success"></i> Microsoft Tile icons for Windows</li>
                <li><i class="fas fa-check text-success"></i> Favicon HTML tags added to include.php</li>
                <li><i class="fas fa-check text-success"></i> Cross-browser compatibility ensured</li>
            </ul>
        </div>

        <!-- Favicon Preview -->
        <div class="test-section">
            <h3><i class="fas fa-images"></i> Generated Favicon Sizes</h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="favicon-preview">
                        <img src="images/favicons/favicon-16x16.png" alt="16x16 favicon" width="16" height="16">
                        <div class="favicon-size">16×16</div>
                    </div>
                    <div class="favicon-preview">
                        <img src="images/favicons/favicon-32x32.png" alt="32x32 favicon" width="32" height="32">
                        <div class="favicon-size">32×32</div>
                    </div>
                    <div class="favicon-preview">
                        <img src="images/favicons/favicon-48x48.png" alt="48x48 favicon" width="48" height="48">
                        <div class="favicon-size">48×48</div>
                    </div>
                    <div class="favicon-preview">
                        <img src="images/favicons/favicon-64x64.png" alt="64x64 favicon" width="64" height="64">
                        <div class="favicon-size">64×64</div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Apple Touch Icons (iOS)</h5>
                    <div class="favicon-preview">
                        <img src="images/favicons/apple-touch-icon-57x57.png" alt="57x57 apple icon" width="57" height="57">
                        <div class="favicon-size">57×57</div>
                    </div>
                    <div class="favicon-preview">
                        <img src="images/favicons/apple-touch-icon-72x72.png" alt="72x72 apple icon" width="72" height="72">
                        <div class="favicon-size">72×72</div>
                    </div>
                    <div class="favicon-preview">
                        <img src="images/favicons/apple-touch-icon-114x114.png" alt="114x114 apple icon" width="114" height="114">
                        <div class="favicon-size">114×114</div>
                    </div>
                    <div class="favicon-preview">
                        <img src="images/favicons/apple-touch-icon-180x180.png" alt="180x180 apple icon" width="180" height="180">
                        <div class="favicon-size">180×180</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Browser Tab Demo -->
        <div class="test-section">
            <h3><i class="fas fa-window-maximize"></i> Browser Tab Preview</h3>
            <p>This is how the favicon should appear in your browser tab:</p>
            
            <div class="browser-tab-demo">
                <img src="images/favicons/favicon-16x16.png" alt="favicon" width="16" height="16" style="vertical-align: middle; margin-right: 8px;">
                Wishluv Buildcon - Real Estate Solutions
            </div>
            <div class="browser-content">
                <p>The favicon should be visible in the browser tab above. If you don't see it, try:</p>
                <ul>
                    <li>Refreshing the page (Ctrl+F5 or Cmd+Shift+R)</li>
                    <li>Clearing browser cache</li>
                    <li>Closing and reopening the browser</li>
                </ul>
            </div>
        </div>

        <!-- Technical Implementation -->
        <div class="test-section">
            <h3><i class="fas fa-code"></i> Technical Implementation</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Source Logo:</h5>
                    <ul>
                        <li><strong>File:</strong> images/company-logo.png</li>
                        <li><strong>Original Size:</strong> 175×79 pixels</li>
                        <li><strong>Format:</strong> PNG with transparency</li>
                        <li><strong>Usage:</strong> Header logo and favicon source</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Generated Files:</h5>
                    <ul>
                        <li><strong>favicon.ico</strong> - Root directory</li>
                        <li><strong>PNG favicons</strong> - images/favicons/</li>
                        <li><strong>Apple Touch Icons</strong> - images/favicons/</li>
                        <li><strong>Total Files:</strong> 16 favicon variants</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- HTML Code -->
        <div class="test-section">
            <h3><i class="fas fa-file-code"></i> HTML Implementation</h3>
            <p>The following HTML code has been added to <code>include.php</code>:</p>
            <div class="code-block">
&lt;!-- Favicon --&gt;<br>
&lt;link rel="icon" type="image/png" sizes="32x32" href="images/favicons/favicon-32x32.png"&gt;<br>
&lt;link rel="icon" type="image/png" sizes="16x16" href="images/favicons/favicon-16x16.png"&gt;<br>
&lt;link rel="shortcut icon" href="favicon.ico"&gt;<br>
&lt;link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-touch-icon-180x180.png"&gt;<br>
&lt;link rel="apple-touch-icon" sizes="152x152" href="images/favicons/apple-touch-icon-152x152.png"&gt;<br>
&lt;link rel="apple-touch-icon" sizes="144x144" href="images/favicons/apple-touch-icon-144x144.png"&gt;<br>
&lt;meta name="msapplication-TileImage" content="images/favicons/apple-touch-icon-144x144.png"&gt;<br>
&lt;meta name="msapplication-TileColor" content="#4f8f70"&gt;
            </div>
        </div>

        <!-- Testing Instructions -->
        <div class="test-section">
            <h3><i class="fas fa-clipboard-list"></i> Testing Instructions</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Desktop Testing:</h5>
                    <ol>
                        <li>Check browser tab for favicon</li>
                        <li>Bookmark this page and check bookmarks bar</li>
                        <li>Test in different browsers (Chrome, Firefox, Safari, Edge)</li>
                        <li>Check browser history for favicon</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h5>Mobile Testing:</h5>
                    <ol>
                        <li>Open website on mobile browser</li>
                        <li>Add to home screen (iOS/Android)</li>
                        <li>Check the app icon on home screen</li>
                        <li>Test in mobile Safari and Chrome</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Browser Compatibility -->
        <div class="test-section">
            <h3><i class="fas fa-globe"></i> Browser Compatibility</h3>
            <div class="row">
                <div class="col-md-3">
                    <p><i class="fab fa-chrome text-success"></i> <strong>Chrome</strong><br>
                    <small>Supports PNG favicons, multiple sizes</small></p>
                </div>
                <div class="col-md-3">
                    <p><i class="fab fa-firefox text-success"></i> <strong>Firefox</strong><br>
                    <small>Supports PNG and ICO favicons</small></p>
                </div>
                <div class="col-md-3">
                    <p><i class="fab fa-safari text-success"></i> <strong>Safari</strong><br>
                    <small>Supports Apple Touch Icons</small></p>
                </div>
                <div class="col-md-3">
                    <p><i class="fab fa-edge text-success"></i> <strong>Edge</strong><br>
                    <small>Supports PNG and Microsoft Tiles</small></p>
                </div>
            </div>
        </div>

        <!-- Expected Results -->
        <div class="test-section">
            <h3><i class="fas fa-bullseye"></i> Expected Results</h3>
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> What you should see:</h5>
                <ul class="mb-0">
                    <li>✅ Wishluv Buildcon logo appears in browser tab</li>
                    <li>✅ Logo appears when bookmarking the site</li>
                    <li>✅ High-quality icon when adding to mobile home screen</li>
                    <li>✅ Consistent branding across all devices and browsers</li>
                    <li>✅ Professional appearance in browser history and tabs</li>
                </ul>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="test-section">
            <h3><i class="fas fa-link"></i> Quick Test Links</h3>
            <p>Test the favicon on different pages:</p>
            <div class="row">
                <div class="col-md-4">
                    <a href="index.php" class="btn btn-primary btn-sm w-100 mb-2">Home Page</a>
                </div>
                <div class="col-md-4">
                    <a href="about.php" class="btn btn-secondary btn-sm w-100 mb-2">About Page</a>
                </div>
                <div class="col-md-4">
                    <a href="contact.php" class="btn btn-info btn-sm w-100 mb-2">Contact Page</a>
                </div>
            </div>
        </div>
    </div>

    <?php include "vendor.php" ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if favicon is loaded
        function checkFavicon() {
            const favicon = document.querySelector('link[rel="icon"]');
            if (favicon) {
                console.log('✅ Favicon link found:', favicon.href);
                
                // Test if favicon image loads
                const img = new Image();
                img.onload = function() {
                    console.log('✅ Favicon image loaded successfully');
                };
                img.onerror = function() {
                    console.log('❌ Favicon image failed to load');
                };
                img.src = favicon.href;
            } else {
                console.log('❌ No favicon link found');
            }
        }
        
        // Check Apple Touch Icons
        function checkAppleTouchIcons() {
            const appleIcons = document.querySelectorAll('link[rel="apple-touch-icon"]');
            console.log(`Found ${appleIcons.length} Apple Touch Icons`);
            
            appleIcons.forEach((icon, index) => {
                console.log(`Apple Touch Icon ${index + 1}:`, icon.href, icon.sizes);
            });
        }
        
        // Run checks
        setTimeout(() => {
            checkFavicon();
            checkAppleTouchIcons();
            
            // Display current page title in console
            console.log('Current page title:', document.title);
            console.log('Favicon should be visible in the browser tab next to this title');
        }, 1000);
        
        // Add click tracking for test links
        document.querySelectorAll('a[href$=".php"]').forEach(link => {
            link.addEventListener('click', function() {
                console.log('Testing favicon on:', this.href);
            });
        });
    });
    </script>
</body>
</html>
