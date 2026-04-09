<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crystal City Links Test - Wishluv Buildcon</title>
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
        .link-test {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            margin: 0.5rem 0;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #25D366;
        }
        .link-info {
            flex-grow: 1;
        }
        .link-location {
            font-weight: bold;
            color: #333;
        }
        .link-url {
            color: #666;
            font-size: 0.9rem;
            font-family: monospace;
        }
        .test-button {
            background: #25D366;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .test-button:hover {
            background: #128C7E;
            color: white;
            text-decoration: none;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .status-success { background-color: #28a745; }
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
    </style>
</head>
<body>
    <div class="test-container">
        <div class="test-header">
            <h1><i class="fas fa-external-link-alt" style="color: #25D366;"></i> Crystal City Links Test</h1>
            <p class="lead">Testing all Crystal City links redirect to external website</p>
        </div>

        <!-- Link Status Overview -->
        <div class="test-section">
            <h3><i class="fas fa-check-circle"></i> Link Update Status</h3>
            <ul class="feature-list">
                <li><i class="fas fa-check text-success"></i> Footer "Projects" section - Crystal City link updated</li>
                <li><i class="fas fa-check text-success"></i> Navigation menu (desktop) - Crystal City link updated</li>
                <li><i class="fas fa-check text-success"></i> Navigation menu (mobile) - Crystal City link updated</li>
                <li><i class="fas fa-check text-success"></i> Sitemap page - Crystal City link updated</li>
                <li><i class="fas fa-check text-success"></i> All links open in new window/tab</li>
                <li><i class="fas fa-check text-success"></i> Security attributes added (rel="noopener noreferrer")</li>
            </ul>
        </div>

        <!-- Link Testing -->
        <div class="test-section">
            <h3><i class="fas fa-link"></i> Crystal City Links to Test</h3>
            
            <div class="link-test">
                <div class="link-info">
                    <div class="link-location">Footer - Projects Section</div>
                    <div class="link-url">https://crystalcity.wishluvbuildcon.com/</div>
                </div>
                <a href="https://crystalcity.wishluvbuildcon.com/" target="_blank" rel="noopener noreferrer" class="test-button">
                    <i class="fas fa-external-link-alt"></i> Test Link
                </a>
            </div>

            <div class="link-test">
                <div class="link-info">
                    <div class="link-location">Navigation Menu - Projects > Ongoing Projects</div>
                    <div class="link-url">https://crystalcity.wishluvbuildcon.com/</div>
                </div>
                <a href="https://crystalcity.wishluvbuildcon.com/" target="_blank" rel="noopener noreferrer" class="test-button">
                    <i class="fas fa-external-link-alt"></i> Test Link
                </a>
            </div>

            <div class="link-test">
                <div class="link-info">
                    <div class="link-location">Sitemap Page - Projects Section</div>
                    <div class="link-url">https://crystalcity.wishluvbuildcon.com/</div>
                </div>
                <a href="https://crystalcity.wishluvbuildcon.com/" target="_blank" rel="noopener noreferrer" class="test-button">
                    <i class="fas fa-external-link-alt"></i> Test Link
                </a>
            </div>
        </div>

        <!-- Test Instructions -->
        <div class="test-section">
            <h3><i class="fas fa-clipboard-list"></i> Testing Instructions</h3>
            <ol>
                <li><strong>Test Footer Link:</strong> Scroll to the bottom of any page and click "Crystal City" under Projects</li>
                <li><strong>Test Navigation Menu:</strong> Hover over "Projects" → "Ongoing Projects" → Click "Crystal City – Naubatpur"</li>
                <li><strong>Test Mobile Menu:</strong> On mobile, open menu → Projects → Ongoing Projects → Crystal City</li>
                <li><strong>Test Sitemap:</strong> Visit <a href="sitemap.php" target="_blank">sitemap.php</a> and click Crystal City link</li>
                <li><strong>Verify New Window:</strong> Ensure all links open in a new tab/window</li>
                <li><strong>Check URL:</strong> Verify the destination is https://crystalcity.wishluvbuildcon.com/</li>
            </ol>
        </div>

        <!-- Technical Implementation -->
        <div class="test-section">
            <h3><i class="fas fa-code"></i> Technical Implementation</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Link Attributes:</h5>
                    <ul>
                        <li><code>href="https://crystalcity.wishluvbuildcon.com/"</code></li>
                        <li><code>target="_blank"</code> - Opens in new window</li>
                        <li><code>rel="noopener noreferrer"</code> - Security</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Files Modified:</h5>
                    <ul>
                        <li><strong>footer.php</strong> - Projects section</li>
                        <li><strong>navbar.php</strong> - Navigation menus</li>
                        <li><strong>sitemap.php</strong> - Sitemap links</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Security Features -->
        <div class="test-section">
            <h3><i class="fas fa-shield-alt"></i> Security Features</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>rel="noopener":</h5>
                    <p>Prevents the new page from accessing the window.opener property, protecting against potential security vulnerabilities.</p>
                </div>
                <div class="col-md-6">
                    <h5>rel="noreferrer":</h5>
                    <p>Prevents the browser from sending the HTTP referer header, providing additional privacy protection.</p>
                </div>
            </div>
        </div>

        <!-- Expected Behavior -->
        <div class="test-section">
            <h3><i class="fas fa-bullseye"></i> Expected Behavior</h3>
            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle"></i> When clicking any Crystal City link:</h5>
                <ul class="mb-0">
                    <li>✅ Link opens in a new browser tab/window</li>
                    <li>✅ Destination URL is https://crystalcity.wishluvbuildcon.com/</li>
                    <li>✅ Original Wishluv Buildcon website remains open in the original tab</li>
                    <li>✅ No security warnings or issues</li>
                    <li>✅ Works consistently across all browsers and devices</li>
                </ul>
            </div>
        </div>

        <!-- Quick Navigation Test -->
        <div class="test-section">
            <h3><i class="fas fa-mouse-pointer"></i> Quick Navigation Test</h3>
            <p>Use the navigation menu above to test the Crystal City link directly:</p>
            <div class="text-center">
                <p class="text-muted">Projects → Ongoing Projects → Crystal City – Naubatpur</p>
                <small>The link should open https://crystalcity.wishluvbuildcon.com/ in a new tab</small>
            </div>
        </div>
    </div>

    <?php include "vendor.php" ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Track link clicks for testing
        const crystalCityLinks = document.querySelectorAll('a[href*="crystalcity.wishluvbuildcon.com"]');
        
        crystalCityLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                console.log('Crystal City link clicked:', this.href);
                console.log('Target:', this.target);
                console.log('Rel:', this.rel);
                
                // Show confirmation
                setTimeout(() => {
                    if (confirm('Crystal City link clicked! Did it open in a new tab?')) {
                        console.log('✅ Link test successful');
                    } else {
                        console.log('❌ Link test failed');
                    }
                }, 1000);
            });
        });
        
        // Check if links have correct attributes
        setTimeout(() => {
            const linkCount = crystalCityLinks.length;
            console.log(`Found ${linkCount} Crystal City links on this page`);
            
            crystalCityLinks.forEach((link, index) => {
                console.log(`Link ${index + 1}:`, {
                    href: link.href,
                    target: link.target,
                    rel: link.rel,
                    text: link.textContent.trim()
                });
            });
        }, 500);
    });
    </script>
</body>
</html>
