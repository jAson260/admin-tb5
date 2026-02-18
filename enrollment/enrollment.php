<?php 
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// MOCK DATA: In production, these variables would be fetched from your 'trainees' table
$user = [
    'surname' => 'DOE',
    'first_name' => 'JOHN',
    'mi' => 'D',
    'sex' => 'Male',
    'dob' => '1998-05-15',
    'region' => 'Region IV-A',
    'province' => 'Laguna',
    'city' => 'San Pablo City',
    'barangay' => 'Brgy VII-A',
    'sec_school' => 'San Pablo City National High School',
    'sec_year' => '2014',
    'tert_school' => 'Laguna State Polytechnic University',
    'tert_year' => '2018'
];
?>
<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid">
        
        <!-- Page Header Section -->
       <!-- Page Header Section (Centered Institutional Style) -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-dark mb-1">
                    <i class="fas fa-file-signature me-2 text-royal"></i>THE BIG FIVE TRAINING AND ASSESSMENT CENTER INC.
                </h2>
                <p class="text-muted small">Official Enrollment Portal - The Big Five Training Group</p>
                <div class="mx-auto mt-2" style="width: 60px; height: 3px; background-color: var(--royal-blue); border-radius: 2px;"></div>
            </div>
        </div>
        <!-- Enrollment Form Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4 p-md-5">
                <!-- SENIOR DEV FIX: Action updated to enrollment_page2.php -->
                <form action="enrollment_page2.php" method="POST">
                    
                    <!-- INSTITUTIONAL SELECTION -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-school me-2"></i>Institutional Selection</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select border-primary" id="schoolSelect" name="institution" required onchange="checkSchool(this.value)">
                                    <option value="big_five" selected>The Big Five Training and Assessment Center Inc.</option>
                                    <option value="big_blossom">Big Blossom Institute Inc.</option>
                                </select>
                                <label for="schoolSelect">Select School for Enrollment</label>
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4 opacity-50">

                    <!-- QUALIFICATIONS SELECTION -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-graduation-cap me-2"></i>Select Qualification</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <select class="form-select" name="qualification" required>
                                    <option selected disabled value="">Choose from available NC Courses</option>
                                    <option value="BREAD AND PASTRY PRODUCTION NC II">BREAD AND PASTRY PRODUCTION NC II</option>
                                    <option value="COMPUTER SYSTEMS SERVICING NC II">COMPUTER SYSTEMS SERVICING NC II</option>
                                    <option value="ELECTRONIC PRODUCTS ASSEMBLY & SERVICING NC II">ELECTRONIC PRODUCTS ASSEMBLY & SERVICING NC II</option>
                                    <option value="TRAINERS METHODOLOGY LEVEL 1">TRAINERS METHODOLOGY LEVEL 1 (TM 1)</option>
                                </select>
                                <label>Course / Qualification</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 1: ULI -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-fingerprint me-2"></i>Unique Learner's Identifier</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" name="uli" class="form-control" id="uliInput" placeholder="ULI" required>
                                <label for="uliInput">ULI (Unique Learner's Identifier)</label>
                            </div>
                        </div>
                    </div>

  <!-- SECTION 2: NAME OF TRAINEE (LOCKED) -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-user-tag me-2"></i>Name of Trainee <i class="fas fa-lock ms-1 text-muted small"></i></h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-uppercase" value="<?php echo $user['surname']; ?>" readonly>
                                <label>Surname</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-uppercase" value="<?php echo $user['first_name']; ?>" readonly>
                                <label>First Name</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-center text-uppercase" value="<?php echo $user['mi']; ?>" readonly>
                                <label>M.I.</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: PERSONAL DETAILS (LOCKED) -->
                    <h5 class="fw-bold mb-3 text-royal" style="font-size: 1rem;">
                        <i class="fas fa-info-circle me-2"></i>Personal Details <i class="fas fa-lock ms-1 text-muted small"></i>
                    </h5>
                    <div class="row g-2 mb-4"> 
                        <div class="col-md-2">
                            <div class="form-floating form-floating-sm">
                                <!-- Selects are 'disabled' because 'readonly' doesn't work on them -->
                                <select class="form-select bg-light" disabled>
                                    <option selected><?php echo $user['sex']; ?></option>
                                </select>
                                <label>Sex</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="date" class="form-control bg-light" value="<?php echo $user['dob']; ?>" readonly>
                                <label>Date of Birth</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: PLACE OF BIRTH (LOCKED) -->
                    <h5 class="fw-bold mb-2 text-royal" style="font-size: 0.95rem;">
                        <i class="fas fa-map-marker-alt me-2"></i>Complete Address <i class="fas fa-lock ms-1 text-muted small"></i>
                    </h5>
                    <div class="row g-2 mb-4">
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo $user['region']; ?>" readonly>
                                <label>Region</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo $user['province']; ?>" readonly>
                                <label>Province</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo $user['city']; ?>" readonly>
                                <label>Municipality/City</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo $user['barangay']; ?>" readonly>
                                <label>Barangay</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 5: EDUCATIONAL BACKGROUND (LOCKED) -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-graduation-cap me-2"></i>Educational Background <i class="fas fa-lock ms-1 text-muted small"></i></h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-9">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light" value="<?php echo $user['sec_school']; ?>" readonly>
                                <label>Secondary School Attended</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-light text-center" value="<?php echo $user['sec_year']; ?>" readonly>
                                <label>Year Completed</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-9">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light" value="<?php echo $user['tert_school']; ?>" readonly>
                                <label>Tertiary School Attended</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-light text-center" value="<?php echo $user['tert_year']; ?>" readonly>
                                <label>Year Completed</label>
                            </div>
                        </div>
                    </div>

                    
                    <!-- Navigation Button -->
                    <div class="mt-5 text-end border-top pt-4">
                    <div class="text-end border-top pt-4">
                         <p class="text-muted small mb-3 italic">
                            <i class="fas fa-info-circle me-1"></i> 
                         Before proceeding to the next step, Please check first your personal details above.
                         </p>
  
                    </div>
                        <a href="Login/enrollment/enrollment_page2.php" class="text-decoration-none small text-royal fw-bold"></a>
                         <div class="mt-5 text-end border-top pt-4">
                        <button type="submit" class="btn btn-royal rounded-pill px-5 py-3 shadow fw-bold">
                            Proceed to Next Step <i class="fas fa-chevron-right ms-2"></i>
                        </button>
                   
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- AUTO-REDIRECT LOGIC -->
<script>
function checkSchool(value) {
    if (value === 'big_blossom') {
        // Redirection to Big Blossom Subfolder
        window.location.href = 'bigblossomenrollment/bb_enrollment.php';
    }
}
</script>

<?php 
// Go up one level to find the footer
include '../includes/footer.php'; 
?>