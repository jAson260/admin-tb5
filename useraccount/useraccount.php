<?php 
// filepath: c:\laragon\www\admin-tb5\useraccount\useraccount.php

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../db-connect.php');

$msg = "";
$upload_success = false;
$user_data = [];

// --- PROFILE PICTURE UPLOAD LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_avatar'])) {
    $targetDir = "../uploads/profiles/";
    
    // Ensure directory exists
    if (!file_exists($targetDir)) { 
        mkdir($targetDir, 0777, true); 
    }

    $fileType = strtolower(pathinfo($_FILES["new_avatar"]["name"], PATHINFO_EXTENSION));
    $newFileName = "profile_" . $_SESSION['user_id'] . "_" . time() . "." . $fileType;
    $targetFilePath = $targetDir . $newFileName;
    
    // Allowed formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    
    // Validate file type
    if (in_array($fileType, $allowTypes)) {
        // Validate file size (max 5MB)
        if ($_FILES["new_avatar"]["size"] <= 5242880) {
            if (move_uploaded_file($_FILES["new_avatar"]["tmp_name"], $targetFilePath)) {
                try {
                    // Get old profile picture
                    $stmt = $pdo->prepare("SELECT ProfilePicture FROM studentinfos WHERE Id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $oldPic = $stmt->fetchColumn();
                    
                    // Update database with new profile picture
                    $updatePic = $pdo->prepare("UPDATE studentinfos SET ProfilePicture = ? WHERE Id = ?");
                    
                    if ($updatePic->execute([$newFileName, $_SESSION['user_id']])) {
                        // Delete old profile picture if exists and not default
                        if ($oldPic && file_exists($targetDir . $oldPic)) {
                            unlink($targetDir . $oldPic);
                        }
                        
                        $upload_success = true;
                        header("Location: useraccount.php?success=upload");
                        exit;
                    } else {
                        $msg = "Database update failed.";
                        // Delete uploaded file if DB update fails
                        if (file_exists($targetFilePath)) {
                            unlink($targetFilePath);
                        }
                    }
                } catch(PDOException $e) { 
                    $msg = "DB Error: " . $e->getMessage();
                    // Delete uploaded file if error occurs
                    if (file_exists($targetFilePath)) {
                        unlink($targetFilePath);
                    }
                }
            } else { 
                $msg = "Error uploading your file. Please try again."; 
            }
        } else {
            $msg = "File size too large. Maximum 5MB allowed.";
        }
    } else { 
        $msg = "Only JPG, JPEG, PNG & GIF are allowed."; 
    }
}

// Fetch user data from database
try {
    $stmt = $pdo->prepare("
        SELECT 
            Id, ULI, FirstName, LastName, MiddleName, ExtensionName,
            Email, Sex, BirthDate, ContactNo, Age,
            RegionName, ProvinceName, CityName, BarangayName,
            SecondarySchool, SecondaryYearCompleted,
            TertiarySchool, TertiaryYearCompleted,
            Status, ProfilePicture
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
            'secondary_school' => $userData['SecondarySchool'] ?? '',
            'secondary_year' => $userData['SecondaryYearCompleted'] ?? '',
            'tertiary_school' => $userData['TertiarySchool'] ?? '',
            'tertiary_year' => $userData['TertiaryYearCompleted'] ?? '',
            'status' => $userData['Status'],
            'profile_pic' => $userData['ProfilePicture']
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
            header('Location: useraccount.php?success=address');
            exit;
        }
        
    } catch(PDOException $e) {
        $msg = "Error updating profile: " . $e->getMessage();
    }
}

// Check for success messages
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'upload':
            $msg = "Profile picture updated successfully!";
            break;
        case 'address':
            $msg = "Address update request sent to admin for approval!";
            break;
        default:
            $msg = "Update successful!";
    }
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

<!-- Main Container Wrapper -->
<div class="container-fluid py-4">
    <div class="row g-4">
        
        <!-- LEFT SIDE: PROFILE CARD (Avatar & Basic Info) -->
        <div class="col-lg-4 col-md-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 text-center text-md-start">
                
                <!-- Avatar Container -->
                <div class="d-flex flex-column align-items-center align-items-md-start">
                    <div class="position-relative d-inline-block mb-3" style="width: 130px; height: 130px;">
                        <?php 
                            // Set avatar path
                            if (!empty($user_data['profile_pic'])) {
                                $avatarPath = "../uploads/profiles/" . htmlspecialchars($user_data['profile_pic']);
                                // Check if file exists
                                if (!file_exists($avatarPath)) {
                                    $avatarPath = "../img/default-avatar.png";
                                }
                            } else {
                                $avatarPath = "../img/default-avatar.png";
                            }
                        ?>
                        <img src="<?php echo $avatarPath; ?>?v=<?php echo time(); ?>" 
                             alt="Avatar" 
                             id="currentAvatar"
                             class="rounded-circle border border-4 border-white shadow-sm w-100 h-100" 
                             style="object-fit: cover;">
                        
                        <!-- Camera Button -->
                        <button class="btn btn-sm btn-royal position-absolute bottom-0 end-0 rounded-circle border-2 border-white shadow-sm" 
                                style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;"
                                data-bs-toggle="modal" data-bs-target="#uploadModal"
                                type="button">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <!-- Trainee Basic Info -->
                    <div class="mt-2">
                        <h5 class="fw-bold text-dark mb-1 text-uppercase">
                            <?php echo htmlspecialchars($user_data['surname'] . ", " . $user_data['first_name']); ?>
                        </h5>
                        <p class="text-muted mb-2" style="font-size: 0.9rem;">
                            <span class="badge bg-light text-dark border fw-normal">ULI: <?php echo htmlspecialchars($user_data['uli']); ?></span>
                        </p>
                        
                        <!-- Verification Status -->
                        <div class="mt-2">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2" style="font-size: 11px;">
                                <i class="fas fa-check-circle me-1"></i> VERIFIED ACCOUNT
                            </span>
                        </div>
                    </div>
                </div>

                <hr class="my-4 opacity-50">
                <div class="small text-muted">
                    <p class="mb-1"><i class="fas fa-calendar-alt me-2"></i> Registered: <?php echo date('M Y'); ?></p>
                    <p class="mb-0"><i class="fas fa-user-graduate me-2"></i> Active Trainee</p>
                </div>
            </div>
        </div>

        <!-- RIGHT SIDE: INTEGRATED DETAILS FORM -->
        <div class="col-lg-8 col-md-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="mb-0 fw-bold text-royal"><i class="fas fa-edit me-2"></i>Official Personal Details</h6>
                </div>
                <div class="card-body p-4">
                    <form action="useraccount.php" method="POST">
                        <input type="hidden" name="update_profile" value="1">

                        <!-- SECTION 1: NAME OF TRAINEE (Locked) -->
                        <div class="d-flex align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-secondary small text-uppercase">
                                <i class="fas fa-user-tag me-2"></i>Name of Trainee
                            </h6>
                            <span class="ms-2 badge bg-light text-muted border-0" style="font-size: 10px;"><i class="fas fa-lock me-1"></i>LOCKED</span>
                        </div>
                        
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

                        <!-- SECTION 2: PERSONAL DETAILS (Locked) -->
                        <h6 class="fw-bold mb-3 text-secondary small text-uppercase">
                            <i class="fas fa-info-circle me-2"></i>Personal Details
                        </h6>
                        <div class="row g-2 mb-4"> 
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select bg-light" disabled>
                                        <option value="Male" <?php echo ($user_data['sex'] == 'Male') ? 'selected':''; ?>>Male</option>
                                        <option value="Female" <?php echo ($user_data['sex'] == 'Female') ? 'selected':''; ?>>Female</option>
                                    </select>
                                    <label>Sex</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="date" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['dob']); ?>" readonly>
                                    <label>Date of Birth</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['phone']); ?>" readonly>
                                    <label>Phone Number</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['email']); ?>" readonly>
                                    <label>Email</label>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: ADDRESS (Editable) -->
                        <h6 class="fw-bold mb-3 text-secondary small text-uppercase">
                            <i class="fas fa-map-marker-alt me-2"></i>Complete Address
                        </h6>
                        <div class="row g-2 mb-4">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select name="region" class="form-select" id="regionSelect" required>
                                        <option value="NCR" <?php echo ($user_data['region'] == 'NCR') ? 'selected':''; ?>>NCR</option>
                                        <option value="Region IV-A" <?php echo ($user_data['region'] == 'Region IV-A') ? 'selected':''; ?>>Region IV-A</option>
                                    </select>
                                    <label>Region</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select name="province" class="form-select" id="provinceSelect" required>
                                        <option value="Laguna" <?php echo ($user_data['province'] == 'Laguna') ? 'selected':''; ?>>Laguna</option>
                                        <option value="Batangas" <?php echo ($user_data['province'] == 'Batangas') ? 'selected':''; ?>>Batangas</option>
                                    </select>
                                    <label>Province</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select name="city" class="form-select" id="citySelect" required>
                                        <option value="San Pablo" <?php echo ($user_data['city'] == 'San Pablo') ? 'selected':''; ?>>San Pablo</option>
                                        <option value="Calamba" <?php echo ($user_data['city'] == 'Calamba') ? 'selected':''; ?>>Calamba</option>
                                    </select>
                                    <label>Municipality</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select name="barangay" class="form-select" id="brgySelect" required>
                                        <option value="Brgy VII-A" <?php echo ($user_data['barangay'] == 'Brgy VII-A') ? 'selected':''; ?>>Brgy VII-A</option>
                                    </select>
                                    <label>Barangay</label>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 4: EDUCATIONAL BACKGROUND (Locked) -->
                        <h6 class="fw-bold mb-3 text-secondary small text-uppercase">
                            <i class="fas fa-graduation-cap me-2"></i>Educational Background <i class="fas fa-lock ms-1 text-muted small"></i>
                        </h6>
                        <div class="row g-3 mb-3">
                            <div class="col-md-9">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['secondary_school']); ?>" readonly>
                                    <label>Secondary School Attended</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control bg-light text-center" value="<?php echo htmlspecialchars($user_data['secondary_year']); ?>" readonly>
                                    <label>Year Completed</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-9">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user_data['tertiary_school']); ?>" readonly>
                                    <label>Tertiary School (College/Vocational)</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="number" class="form-control bg-light text-center" value="<?php echo htmlspecialchars($user_data['tertiary_year']); ?>" readonly>
                                    <label>Year Completed</label>
                                </div>
                            </div>
                        </div>

                        <!-- ACTION BUTTON -->
                        <div class="text-end border-top pt-4 mt-2">
                            <p class="text-muted small mb-3 fst-italic">
                                <i class="fas fa-info-circle me-1 text-primary"></i> 
                                Some fields are locked. To update official records, please contact the Admin.
                            </p>
                            <button type="button" id="btnSubmitApproval" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm">
                                Send to Admin for Approval <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PROFILE PICTURE UPLOAD MODAL -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom-0 p-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-image me-2"></i>Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="useraccount.php" method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="modal-body px-4 text-center">
                    <div class="mb-3">
                        <img id="imgPreview" src="<?php echo $avatarPath; ?>" class="rounded-circle border shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <label class="btn btn-outline-primary rounded-pill px-4 mb-2" style="cursor: pointer;">
                        <i class="fas fa-file-import me-2"></i> Select New Image
                        <input type="file" name="new_avatar" id="avatarInput" hidden accept="image/jpeg,image/jpg,image/png,image/gif" required onchange="previewImage(this);">
                    </label>
                    <p class="text-muted mb-0" style="font-size: 11px;">Recommended: Square image (e.g. 500x500px)</p>
                    <p class="text-muted" style="font-size: 11px;">Max size: 5MB | Formats: JPG, PNG, GIF</p>
                    <div id="uploadError" class="text-danger small mt-2" style="display: none;"></div>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0 justify-content-center">
                    <button type="submit" class="btn btn-royal rounded-pill px-5 shadow-sm fw-bold">
                        <i class="fas fa-upload me-2"></i>Upload Picture
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ADDRESS UPDATE APPROVAL MODAL -->
<div class="modal fade" id="approvalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mx-auto mb-4 bg-success bg-opacity-10 d-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px;">
                    <i class="fas fa-check-circle text-success" style="font-size: 60px;"></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-2">Successfully Sent!</h4>
                <p class="text-muted mb-4">Your request has been submitted. Wait for the admin's approval to finalize your record updates.</p>
                
                <button type="button" class="btn btn-royal rounded-pill px-5 py-2 fw-bold shadow-sm" id="modalOkBtn">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Preview image before upload
function previewImage(input) {
    const errorDiv = document.getElementById('uploadError');
    errorDiv.style.display = 'none';
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size (5MB)
        if (file.size > 5242880) {
            errorDiv.textContent = 'File size exceeds 5MB. Please choose a smaller image.';
            errorDiv.style.display = 'block';
            input.value = '';
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            errorDiv.textContent = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
            errorDiv.style.display = 'block';
            input.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imgPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}

// Address update approval modal
document.addEventListener("DOMContentLoaded", function() {
    const btnSubmit = document.getElementById('btnSubmitApproval');
    const approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
    const modalOkBtn = document.getElementById('modalOkBtn');
    const profileForm = btnSubmit.closest('form');

    btnSubmit.addEventListener('click', function() {
        if (profileForm.checkValidity()) {
            approvalModal.show();
        } else {
            profileForm.reportValidity();
        }
    });

    modalOkBtn.addEventListener('click', function() {
        profileForm.submit(); 
    });
});

// Auto-hide success alerts after 5 seconds
<?php if ($msg != ""): ?>
setTimeout(function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 5000);
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
