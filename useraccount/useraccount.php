<?php 
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// Mock Data representing the fetchable structure (No SQL used as requested)
$user_data = [
    'uli' => 'REG-2024-000123',
    'surname' => 'DOE',
    'first_name' => 'JOHN',
    'mi' => 'D',
    'sex' => 'Male',
    'dob' => '1998-05-15',
    'phone' => '639945698522',
    'region' => 'Region IV-A',
    'province' => 'Laguna',
    'city' => 'San Pablo City',
    'barangay' => 'Brgy VII-A',
    'secondary_school' => 'San Pablo City National High School',
    'secondary_year' => '2014',
    'tertiary_school' => 'Laguna State Polytechnic University',
    'tertiary_year' => '2018',
    'profile_pic' => '../img/logo1.png' 
];

$msg = "";
if(isset($_POST['update_profile'])){
    $msg = "Your update request has been sent to the Admin for approval.";
}
?>

<div class="main-content">
    <div class="container-fluid">
        
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold"><i class="fas fa-user-cog me-2 text-royal"></i>Official Trainee Profile</h3>
                <p class="text-muted small">Manage your information. Note: Identity details marked with a <i class="fas fa-lock mx-1"></i> can only be changed by Admin.</p>
            </div>
        </div>

        <?php if($msg != ""): ?>
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-info-circle me-2"></i> <?php echo $msg; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- LEFT SIDE: PROFILE CARD -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center p-4 mb-4 rounded-4">
                    <div class="position-relative d-inline-block mx-auto">
                        <img src="<?php echo $user_data['profile_pic']; ?>" 
                             alt="Avatar" 
                             class="rounded-circle border border-4 border-white shadow-sm" 
                             style="width: 140px; height: 140px; object-fit: cover;">
                        <button class="btn btn-sm btn-royal position-absolute bottom-0 end-0 rounded-circle" 
                                data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="mt-3 mb-0 fw-bold"><?php echo $user_data['surname'] . ", " . $user_data['first_name'] . " " . $user_data['mi'] . "."; ?></h5>
                    <p class="text-muted small mb-3">ULI: <?php echo $user_data['uli']; ?></p>
                    <span class="badge bg-success rounded-pill px-3 py-2">Verified Student Account</span>
                </div>
            </div>

            <!-- RIGHT SIDE: INTEGRATED DETAILS FORM -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-5">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-edit me-2"></i>Update Official Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="useraccount.php" method="POST">

                            <!-- SECTION: NAME OF TRAINEE (Read Only) -->
                            <h6 class="fw-bold mb-3 text-secondary small text-uppercase">
                                <i class="fas fa-user-tag me-2"></i>Name of Trainee <i class="fas fa-lock ms-1"></i>
                            </h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light text-uppercase" value="<?php echo $user_data['surname']; ?>" readonly>
                                        <label>Surname</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light text-uppercase" value="<?php echo $user_data['first_name']; ?>" readonly>
                                        <label>First Name</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light text-uppercase text-center" value="<?php echo $user_data['mi']; ?>" readonly>
                                        <label>M.I.</label>
                                    </div>
                                </div>
                            </div>

    <!-- SECTION 3: PERSONAL DETAILS (OFFICIAL RECORD - LOCKED) -->
<h6 class="fw-bold mb-3 text-secondary small text-uppercase">
    <i class="fas fa-info-circle me-2"></i>Personal Details <i class="fas fa-lock ms-1 text-muted small"></i>
</h6>

<div class="row g-2 mb-4"> 
    <!-- Sex Selection - LOCKED -->
    <div class="col-md-2">
        <div class="form-floating form-floating-sm">
            <!-- Note: Selects use 'disabled' and bg-light for the locked look -->
            <select name="sex" class="form-select bg-light" id="sexSelect" disabled>
                <option value="Male" <?php echo ($user_data['sex'] == 'Male') ? 'selected':''; ?>>Male</option>
                <option value="Female" <?php echo ($user_data['sex'] == 'Female') ? 'selected':''; ?>>Female</option>
            </select>
            <label for="sexSelect">Sex</label>
        </div>
    </div>
    
    <!-- Date of Birth - LOCKED -->
    <div class="col-md-3">
        <div class="form-floating form-floating-sm">
            <input type="date" name="dob" class="form-control bg-light" id="dobInput" value="<?php echo $user_data['dob']; ?>" readonly>
            <label for="dobInput">Date of Birth</label>
        </div>
    </div>

    <!-- Phone Number - NEW & LOCKED -->
    <div class="col-md-4">
        <div class="form-floating form-floating-sm">
            <input type="text" name="phone" class="form-control bg-light" id="phoneInput" value="<?php echo $user_data['phone']; ?>" placeholder="639..." readonly>
            <label for="phoneInput">Phone Number (ex. 639945698522)</label>
        </div>
    </div>
</div>

                            <!-- SECTION: PLACE OF BIRTH -->
                            <h6 class="fw-bold mb-3 text-secondary small text-uppercase">
                                <i class="fas fa-map-marker-alt me-2"></i>Complete Address
                            </h6>
                            <div class="row g-2 mb-4">
                                <div class="col-md-3">
                                    <div class="form-floating form-floating-sm">
                                        <select name="region" class="form-select" id="regionSelect" required>
                                            <option value="NCR" <?php echo ($user_data['region'] == 'NCR') ? 'selected':''; ?>>NCR</option>
                                            <option value="Region IV-A" <?php echo ($user_data['region'] == 'Region IV-A') ? 'selected':''; ?>>Region IV-A</option>
                                        </select>
                                        <label for="regionSelect">Region</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating form-floating-sm">
                                        <select name="province" class="form-select" id="provinceSelect" required>
                                            <option value="Laguna" <?php echo ($user_data['province'] == 'Laguna') ? 'selected':''; ?>>Laguna</option>
                                            <option value="Batangas" <?php echo ($user_data['province'] == 'Batangas') ? 'selected':''; ?>>Batangas</option>
                                        </select>
                                        <label for="provinceSelect">Province</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating form-floating-sm">
                                        <select name="city" class="form-select" id="citySelect" required>
                                            <option value="San Pablo City" <?php echo ($user_data['city'] == 'San Pablo City') ? 'selected':''; ?>>San Pablo City</option>
                                        </select>
                                        <label for="citySelect">Municipality/City</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating form-floating-sm">
                                        <select name="barangay" class="form-select" id="brgySelect" required>
                                            <option value="Brgy VII-A" <?php echo ($user_data['barangay'] == 'Brgy VII-A') ? 'selected':''; ?>>Brgy VII-A</option>
                                        </select>
                                        <label for="brgySelect">Barangay</label>
                                    </div>
                                </div>
                            </div>

                           <!-- SECTION 5: EDUCATIONAL BACKGROUND (OFFICIAL RECORD - LOCKED) -->
<h6 class="fw-bold mb-3 text-secondary small text-uppercase">
    <i class="fas fa-graduation-cap me-2"></i>Educational Background <i class="fas fa-lock ms-1"></i>
</h6>

<div class="row g-3 mb-3">
    <!-- Secondary School - LOCKED -->
    <div class="col-md-9">
        <div class="form-floating">
            <input type="text" name="secondary_school" class="form-control bg-light" id="secSchool" 
                   value="<?php echo $user_data['secondary_school']; ?>" readonly>
            <label for="secSchool">Secondary School Attended</label>
        </div>
    </div>
    <!-- Year Completed - LOCKED -->
    <div class="col-md-3">
        <div class="form-floating">
            <input type="number" name="secondary_year" class="form-control bg-light text-center" id="secYear" 
                   value="<?php echo $user_data['secondary_year']; ?>" readonly>
            <label for="secYear">Year Completed</label>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Tertiary School - LOCKED -->
    <div class="col-md-9">
        <div class="form-floating">
            <input type="text" name="tertiary_school" class="form-control bg-light" id="tertSchool" 
                   value="<?php echo $user_data['tertiary_school']; ?>" readonly>
            <label for="tertSchool">Tertiary School Attended (College / Vocational)</label>
        </div>
    </div>
    <!-- Year Completed - LOCKED -->
    <div class="col-md-3">
        <div class="form-floating">
            <input type="number" name="tertiary_year" class="form-control bg-light text-center" id="tertYear" 
                   value="<?php echo $user_data['tertiary_year']; ?>" readonly>
            <label for="tertYear">Year Completed</label>
        </div>
    </div>
</div>

<div class="text-end border-top pt-4">
    <p class="text-muted small mb-3 italic">
        <i class="fas fa-info-circle me-1"></i> 
        Some fields are locked. To update your official records, click the button below to notify the Admin.
    </p>
  
</div>
                            <!-- ACTION BUTTON -->
                            <div class="text-end border-top pt-4">
                                <button type="submit" name="update_profile" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm">
                                    Send to Admin for Approval <i class="fas fa-paper-plane ms-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Profile Picture -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0 p-4">
                <h5 class="modal-title fw-bold">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="useraccount.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body px-4 pb-4">
                    <input type="file" name="new_avatar" class="form-control rounded-3" accept="image/*" required>
                    <div class="form-text mt-2">Uploading a new picture will notify the Admin for verification.</div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-royal rounded-pill px-4">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>