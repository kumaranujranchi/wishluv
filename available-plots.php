<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Plots - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .plot-item-card {
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .plot-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        .plot-img-box {
            position: relative;
            height: 200px;
        }
        .plot-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .plot-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(255,255,255,0.9);
            color: #333;
        }
        .plot-price-tag {
            color: var(--Bean-Red);
            font-weight: 700;
            font-size: 1.25rem;
        }
    </style>
</head>

<body>
    <?php include "./navbar.php" ?>
    
    <section class="common-bg">
        <div class="container">
            <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Available Plots</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="about-headline text-center mb-5">
                <h5>Our Inventory</h5>
                <h3 class="home-title">Find Your <span>Perfect Plot</span></h3>
                <p class="mt-3">Whether you're looking for a residential plot to build your dream home or a high-growth investment opportunity, we have a diverse range of plots available across Patna's premier locations.</p>
            </div>

            <div class="row">
                <!-- Plot 1 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card plot-item-card border-0 shadow-sm h-100">
                        <div class="plot-img-box">
                            <img src="./images/plot-1.webp" alt="Residential Plot">
                            <span class="plot-badge">Residential</span>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Officers Colony Plots</h4>
                            <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Chiraura, Patna South</p>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Area: 1200 - 1800 Sq. Ft.</li>
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Black Top Roads</li>
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Secure Gated Perimeter</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="plot-price-tag">₹16.5 L onwards</span>
                                <a href="officers-colony.php" class="btn btn-sm btn-outline-danger px-3">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plot 2 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card plot-item-card border-0 shadow-sm h-100">
                        <div class="plot-img-box">
                            <img src="./images/plot-2.webp" alt="Residential Plot">
                            <span class="plot-badge">Investment</span>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Crystal City Plots</h4>
                            <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Naubatpur, Patna</p>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Area: 600 - 3000 Sq. Ft.</li>
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Planned Township</li>
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Near Future Highway</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="plot-price-tag">₹8.5 L onwards</span>
                                <a href="https://crystalcity.wishluvbuildcon.com/" target="_blank" class="btn btn-sm btn-outline-danger px-3">View Website</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plot 3 -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card plot-item-card border-0 shadow-sm h-100">
                        <div class="plot-img-box">
                            <img src="./images/plot-3.webp" alt="Residential Plot">
                            <span class="plot-badge">Residential</span>
                        </div>
                        <div class="card-body p-4">
                            <h4 class="mb-2">Wish Town Plots</h4>
                            <p class="text-muted small mb-3"><i class="fas fa-map-marker-alt me-1 text-danger"></i> Patut Bihta, Patna</p>
                            <ul class="list-unstyled small mb-3">
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Area: 1000 - 2000 Sq. Ft.</li>
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Modern Amenities</li>
                                <li class="mb-1"><i class="fas fa-check text-success me-2"></i> Peaceful Environment</li>
                            </ul>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="plot-price-tag">₹12.0 L onwards</span>
                                <a href="wishluv-town.php" class="btn btn-sm btn-outline-danger px-3">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <p class="mb-3">Looking for a specific plot size or budget?</p>
                <a href="contact.php" class="btn btn-danger rounded-pill px-5 py-3">Inquire About Plots</a>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>
</body>

</html>