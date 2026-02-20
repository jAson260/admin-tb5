<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../db-connect.php');

$msg = "";
$user_data = [];

try {
    // Fetch user data from database
    $stmt = $pdo->prepare("
        SELECT 
            Id, ULI, FirstName, LastName, MiddleName, ExtensionName,
            Email, Sex, BirthDate, ContactNo, Age,
            RegionName, ProvinceName, CityName, BarangayName,
            Status
        FROM studentinfos 
        WHERE Id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
    
    if ($userData) {
        // Map database fields to display format
        $user_data = [
            'id' => $userData['Id'],
            'uli' => $userData['ULI'],
            'first_name' => $userData['FirstName'],
            'surname' => $userData['LastName'],
            'mi' => $userData['MiddleName'],
            'extension' => $userData['ExtensionName'],
            'email' => $userData['Email'],
            'sex' => $userData['Sex'],
            'dob' => date('Y-m-d', strtotime($userData['BirthDate'])),
            'phone' => $userData['ContactNo'],
            'age' => $userData['Age'],
            'region' => $userData['RegionName'],
            'province' => $userData['ProvinceName'],
            'city' => $userData['CityName'],
            'barangay' => $userData['BarangayName'],
            'status' => $userData['Status'],
            'profile_pic' => '../img/default-avatar.png', // Default avatar
            'secondary_school' => '',
            'secondary_year' => '',
            'tertiary_school' => '',
            'tertiary_year' => ''
        ];
    } else {
        // User not found
        $_SESSION['logged_in'] = false;
        header('Location: ../login/login.php');
        exit;
    }
    
} catch(PDOException $e) {
    $msg = "Error loading profile: " . $e->getMessage();
}

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $region = trim($_POST['region'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $barangay = trim($_POST['barangay'] ?? '');
        
        $updateStmt = $pdo->prepare("
            UPDATE studentinfos 
            SET RegionName = ?, ProvinceName = ?, CityName = ?, BarangayName = ?
            WHERE Id = ?
        ");
        
        if ($updateStmt->execute([$region, $province, $city, $barangay, $_SESSION['user_id']])) {
            $msg = "Address update request sent to admin for approval!";
            // Refresh page to show updated data
            header('Location: useraccount.php?success=1');
            exit;
        }
        
    } catch(PDOException $e) {
        $msg = "Error updating profile: " . $e->getMessage();
    }
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_avatar'])) {
    // TODO: Implement file upload logic
    $msg = "Profile picture upload request sent to admin!";
}

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
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
                <i class="fas fa-info-circle me-2"></i> <?php echo htmlspecialchars($msg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- LEFT SIDE: PROFILE CARD -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm text-center p-4 mb-4 rounded-4">
                    <div class="position-relative d-inline-block mx-auto">
                        <img src="<?php echo htmlspecialchars($user_data['profile_pic']); ?>" 
                             alt="Avatar" 
                             class="rounded-circle border border-4 border-white shadow-sm" 
                             style="width: 140px; height: 140px; object-fit: cover;">
                        <button class="btn btn-sm btn-royal position-absolute bottom-0 end-0 rounded-circle" 
                                data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <h5 class="mt-3 mb-0 fw-bold">
                        <?php 
                        echo htmlspecialchars($user_data['surname'] . ", " . $user_data['first_name']);
                        if (!empty($user_data['mi'])) echo " " . htmlspecialchars($user_data['mi']) . ".";
                        if (!empty($user_data['extension'])) echo " " . htmlspecialchars($user_data['extension']);
                        ?>
                    </h5>
                    <p class="text-muted small mb-3">ULI: <?php echo htmlspecialchars($user_data['uli']); ?></p>
                    <span class="badge bg-<?php echo ($user_data['status'] === 'Approved') ? 'success' : 'warning'; ?> rounded-pill px-3 py-2">
                        <?php echo htmlspecialchars($user_data['status']); ?> Account
                    </span>
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
                                        <input type="text" class="form-control bg-light text-uppercase" value="<?php echo htmlspecialchars($user_data['surname']); ?>" readonly>
                                        <label>Surname</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light text-uppercase" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" readonly>
                                        <label>First Name</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light text-uppercase text-center" value="<?php echo htmlspecialchars($user_data['mi']); ?>" readonly>
                                        <label>M.I.</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <input type="text" class="form-control bg-light text-uppercase text-center" value="<?php echo htmlspecialchars($user_data['extension']); ?>" readonly>
                                        <label>Ext.</label>
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
            <input type="date" name="dob" class="form-control bg-light" id="dobInput" value="<?php echo htmlspecialchars($user_data['dob']); ?>" readonly>
            <label for="dobInput">Date of Birth</label>
        </div>
    </div>

    <!-- Phone Number - LOCKED -->
    <div class="col-md-4">
        <div class="form-floating form-floating-sm">
            <input type="text" name="phone" class="form-control bg-light" id="phoneInput" value="<?php echo htmlspecialchars($user_data['phone']); ?>" placeholder="639..." readonly>
            <label for="phoneInput">Phone Number</label>
        </div>
    </div>

    <!-- Email - LOCKED -->
    <div class="col-md-3">
        <div class="form-floating form-floating-sm">
            <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
            <label>Email</label>
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
                                            <option value="San Pablo" <?php echo ($user_data['city'] == 'San Pablo') ? 'selected':''; ?>>San Pablo</option>
                                            <option value="Calamba" <?php echo ($user_data['city'] == 'Calamba') ? 'selected':''; ?>>Calamba</option>
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
    <div class="col-md-9">
        <div class="form-floating">
            <input type="text" name="secondary_school" class="form-control bg-light" id="secSchool" 
                   value="<?php echo htmlspecialchars($user_data['secondary_school']); ?>" readonly>
            <label for="secSchool">Secondary School Attended</label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-floating">
            <input type="number" name="secondary_year" class="form-control bg-light text-center" id="secYear" 
                   value="<?php echo htmlspecialchars($user_data['secondary_year']); ?>" readonly>
            <label for="secYear">Year Completed</label>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-9">
        <div class="form-floating">
            <input type="text" name="tertiary_school" class="form-control bg-light" id="tertSchool" 
                   value="<?php echo htmlspecialchars($user_data['tertiary_school']); ?>" readonly>
            <label for="tertSchool">Tertiary School Attended (College / Vocational)</label>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-floating">
            <input type="number" name="tertiary_year" class="form-control bg-light text-center" id="tertYear" 
                   value="<?php echo htmlspecialchars($user_data['tertiary_year']); ?>" readonly>
            <label for="tertYear">Year Completed</label>
        </div>
    </div>
</div>

                            <!-- ACTION BUTTON -->
                            <div class="text-end border-top pt-4">
                                <p class="text-muted small mb-3 fst-italic">
                                    <i class="fas fa-info-circle me-1"></i> 
                                    Some fields are locked. To update your official records, click the button below to notify the Admin.
                                </p>
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