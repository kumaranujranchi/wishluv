<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sitemap - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .sitemap-list {
            list-style: none;
            padding-left: 0;
        }
        .sitemap-list li {
            margin-bottom: 10px;
        }
        .sitemap-list a {
            color: #333;
            text-decoration: none;
            transition: color 0.3s;
        }
        .sitemap-list a:hover {
            color: var(--Bean-Red);
        }
        .sitemap-section h4 {
            border-bottom: 2px solid var(--Bean-Red);
            display: inline-block;
            margin-bottom: 20px;
            padding-bottom: 5px;
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
                    <li class="breadcrumb-item active" aria-current="page">Sitemap</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="about-headline text-center mb-5">
                <h3>Site <span>Structure</span></h3>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4 sitemap-section">
                    <h4>Main Pages</h4>
                    <ul class="sitemap-list">
                        <li><a href="index.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Home</a></li>
                        <li><a href="about.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> About Us</a></li>
                        <!-- <li><a href="contact-us.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Contact Us</a></li> -->
                        <li><a href="faq.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> FAQ</a></li>
                        <li><a href="sitemap.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Sitemap</a></li>
                    </ul>
                </div>

                <div class="col-md-4 mb-4 sitemap-section">
                    <h4>Our Projects</h4>
                    <ul class="sitemap-list">
                        <li><a href="featured.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Featured Projects</a></li>
                        <li><a href="available-plots.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Available Plots</a></li>
                        <li><a href="officers-colony.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Officers Colony</a></li>
                        <li><a href="wishluv-town.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Wish Town</a></li>
                        <li><a href="https://crystalcity.wishluvbuildcon.com/"><i class="fas fa-chevron-right me-2 text-danger small"></i> Crystal City</a></li>
                    </ul>
                </div>

                <div class="col-md-4 mb-4 sitemap-section">
                    <h4>Career & Services</h4>
                    <ul class="sitemap-list">
                        <li><a href="plot-service.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Plot Services</a></li>
                        <li><a href="investment-benefit.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Investment Benefits</a></li>
                        <li><a href="current-openings.php"><i class="fas fa-chevron-right me-2 text-danger small"></i> Current Openings</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>
</body>

</html>