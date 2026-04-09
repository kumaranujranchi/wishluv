<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Now - Wishluv Buildcon</title>
    <?php include "include.php" ?>
    <style>
        .application-form-card {
            border-radius: 15px;
            overflow: hidden;
        }
        .form-section-title {
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
                    <li class="breadcrumb-item"><a href="wishluv-career.php">Career</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Apply Now</li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="common-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="about-headline text-center mb-5">
                        <h5>Join Our Team</h5>
                        <h3 class="home-title"><span>Career Application</span> Form</h3>
                        <p class="mt-3">Ready to start your journey with Wishluv Buildcon? Fill out the form below and upload your resume. Our HR team will review your application and contact you if your profile matches our requirements.</p>
                    </div>

                    <div class="card shadow border-0 application-form-card">
                        <div class="card-body p-4 p-md-5">
                            <form id="applyForm" class="wishluv-form" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="form-section-title">Personal Information</h5>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">Current City</label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="qualification" class="form-label">Highest Qualification</label>
                                        <select class="form-select" id="qualification" name="qualification" required>
                                            <option value="" selected disabled>Select Qualification</option>
                                            <option value="1">10th</option>
                                            <option value="2">12th</option>
                                            <option value="3">Graduate</option>
                                            <option value="4">Post Graduate</option>
                                        </select>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <h5 class="form-section-title">Professional Details</h5>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="position" class="form-label">Position Applying For</label>
                                        <input type="text" class="form-control" id="position" name="position" placeholder="e.g. Sales Executive, Manager" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="current_salary" class="form-label">Current Salary (Monthly)</label>
                                        <input type="text" class="form-control" id="current_salary" name="current_salary">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="expected_salary" class="form-label">Expected Salary (Monthly)</label>
                                        <input type="text" class="form-control" id="expected_salary" name="expected_salary" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label for="resume" class="form-label">Upload Resume (PDF/DOC/DOCX)</label>
                                        <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                                    </div>
                                </div>
                                
                                <!-- Honeypot Field (Hidden from users) -->
                                <div style="display:none;">
                                    <input type="text" name="website" id="website">
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-danger px-5 py-3 rounded-pill">Submit Application</button>
                                </div>
                                <div id="formResponse" class="mt-4"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include "footer.php" ?>
    <?php include "vendor.php" ?>

    <script>
    document.getElementById('applyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const responseDiv = document.getElementById('formResponse');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        fetch('api/job_application.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                responseDiv.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>${data.message}</div>`;
                this.reset();
            } else {
                responseDiv.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${data.message}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            responseDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Application';
        });
    });
    </script>
</body>

</html>