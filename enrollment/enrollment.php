<?php 
session_start();
require_once('../includes/rbac-guard.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login/login.php');
    exit;
}

require_once('../db-connect.php');

$user = [];

try {
    // Fetch user data from database
    $stmt = $pdo->prepare("
        SELECT 
            ULI, FirstName, LastName, MiddleName, ExtensionName,
            Sex, BirthDate,
            RegionName, ProvinceName, CityName, BarangayName,
            SecondarySchool, SecondaryYearCompleted,
            TertiarySchool, TertiaryYearCompleted,
            Status
        FROM studentinfos 
        WHERE Id = ?
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userData = $stmt->fetch();
    
    if ($userData) {
        // Map database fields to display format
        $user = [
            'uli' => $userData['ULI'],
            'surname' => strtoupper($userData['LastName']),
            'first_name' => strtoupper($userData['FirstName']),
            'mi' => strtoupper($userData['MiddleName']),
            'extension' => $userData['ExtensionName'],
            'sex' => $userData['Sex'],
            'dob' => date('Y-m-d', strtotime($userData['BirthDate'])),
            'region' => $userData['RegionName'],
            'province' => $userData['ProvinceName'],
            'city' => $userData['CityName'],
            'barangay' => $userData['BarangayName'],
            'sec_school' => $userData['SecondarySchool'],
            'sec_year' => $userData['SecondaryYearCompleted'],
            'tert_school' => $userData['TertiarySchool'],
            'tert_year' => $userData['TertiaryYearCompleted'],
            'status' => $userData['Status']
        ];
    } else {
        // User not found, redirect to login
        $_SESSION['logged_in'] = false;
        header('Location: ../login/login.php');
        exit;
    }
    
} catch(PDOException $e) {
    die("Error loading user data: " . $e->getMessage());
}

include '../includes/header.php'; 
include '../includes/sidebar.php'; 
?>

<!-- Main content area -->
<div class="main-content">
    <div class="container-fluid">
        
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

        <!-- Account Status Alert -->
        <?php if ($user['status'] !== 'Approved'): ?>
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> 
            <strong>Account Status: <?php echo htmlspecialchars($user['status']); ?></strong> - 
            Your account is pending admin approval. Enrollment will be available once approved.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <!-- Enrollment Form Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-body p-4 p-md-5">
                <form action="/enrollment/step-2" method="POST">

                    <hr class="mb-4 opacity-50">

                    <!-- SECTION 1: ULI (Pre-filled and Read-only) -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-fingerprint me-2"></i>Unique Learner's Identifier</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" name="uli" class="form-control bg-light" id="uliInput" value="<?php echo htmlspecialchars($user['uli']); ?>" readonly>
                                <label for="uliInput">ULI (Unique Learner's Identifier)</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: NAME OF TRAINEE (LOCKED) -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-user-tag me-2"></i>Name of Trainee <i class="fas fa-lock ms-1 text-muted small"></i></h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-uppercase" value="<?php echo htmlspecialchars($user['surname']); ?>" readonly>
                                <label>Surname</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-uppercase" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                                <label>First Name</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-center text-uppercase" value="<?php echo htmlspecialchars($user['mi']); ?>" readonly>
                                <label>M.I.</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light text-center text-uppercase" value="<?php echo htmlspecialchars($user['extension']); ?>" readonly>
                                <label>Ext.</label>
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
                                <select class="form-select bg-light" disabled>
                                    <option selected><?php echo htmlspecialchars($user['sex']); ?></option>
                                </select>
                                <label>Sex</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="date" class="form-control bg-light" value="<?php echo htmlspecialchars($user['dob']); ?>" readonly>
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
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['region']); ?>" readonly>
                                <label>Region</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['province']); ?>" readonly>
                                <label>Province</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['city']); ?>" readonly>
                                <label>Municipality/City</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating form-floating-sm">
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['barangay']); ?>" readonly>
                                <label>Barangay</label>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 5: EDUCATIONAL BACKGROUND (LOCKED) -->
                    <h5 class="fw-bold mb-3 text-royal"><i class="fas fa-graduation-cap me-2"></i>Educational Background <i class="fas fa-lock ms-1 text-muted small"></i></h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-9">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['sec_school']); ?>" readonly>
                                <label>Secondary School Attended</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-light text-center" value="<?php echo htmlspecialchars($user['sec_year']); ?>" readonly>
                                <label>Year Completed</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-9">
                            <div class="form-floating">
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($user['tert_school']); ?>" readonly>
                                <label>Tertiary School Attended (College / Vocational)</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="number" class="form-control bg-light text-center" value="<?php echo htmlspecialchars($user['tert_year']); ?>" readonly>
                                <label>Year Completed</label>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Button -->
                    <div class="text-end border-top pt-4">
                        <p class="text-muted small mb-3 fst-italic">
                            <i class="fas fa-info-circle me-1"></i> 
                            Before proceeding to the next step, please check your personal details above.
                        </p>
                        <button type="submit" class="btn btn-royal rounded-pill px-5 py-3 shadow fw-bold" <?php echo ($user['status'] !== 'Approved') ? 'disabled' : ''; ?>>
                            Proceed to Next Step <i class="fas fa-chevron-right ms-2"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>