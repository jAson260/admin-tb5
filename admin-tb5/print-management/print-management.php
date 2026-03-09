<?php
session_start();
require_once('../../includes/rbac-guard.php');
require_once('../../db-connect.php');
checkAdmin();

// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');
?>

<style>
    .nav-tabs .nav-link {
        color: #333;
        border: none;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #4169E1;
        background-color: rgba(65, 105, 225, 0.1);
    }
    
    .nav-tabs .nav-link.active {
        background-color: #4169E1 !important;
        color: white !important;
        border: none;
        border-bottom: 3px solid #2948b8;
    }
</style>

<div class="content-wrapper">
    <div class="main-content">
        <!-- Page Title Card -->
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold text-white mb-2">
                            <i class="bi bi-printer me-2"></i>Print Management
                        </h2>
                        <p class="text-white-50 mb-0">
                            Generate and print documents, TOR, and certificates in batch or individually
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-light btn-sm">
                            <i class="bi bi-clock-history me-1"></i> Print History
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Options Tabs -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="batch-print-tab" data-bs-toggle="tab" data-bs-target="#batch-print" type="button" role="tab">
                            <i class="bi bi-collection me-1"></i> Batch Print
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="individual-print-tab" data-bs-toggle="tab" data-bs-target="#individual-print" type="button" role="tab">
                            <i class="bi bi-person me-1"></i> Individual Print
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tor-print-tab" data-bs-toggle="tab" data-bs-target="#tor-print" type="button" role="tab">
                            <i class="bi bi-file-earmark-text me-1"></i> TOR Print
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- Batch Print Tab -->
                    <div class="tab-pane fade show active" id="batch-print" role="tabpanel">
                        <h5 class="fw-bold mb-3">Batch Print Options</h5>
                        <p class="text-muted">Print approved documents in batch by school, course, or batch.</p>

                        <div class="row g-4">
                               <!-- Print by School -->
                            <div class="col-md-4">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-bank text-primary" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h5 class="fw-bold">Print by School</h5>
                                        <p class="text-muted small">Print all approved documents for a specific school</p>
                                        <button class="btn btn-primary" onclick="showPrintBySchoolModal()">
                                            <i class="bi bi-printer me-2"></i>Select School
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Print by Course -->
                            <div class="col-md-4">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-book text-success" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h5 class="fw-bold">Print by Course</h5>
                                        <p class="text-muted small">Print all approved documents for a specific course</p>
                                        <button class="btn btn-success" onclick="showPrintByCourseModal()">
                                            <i class="bi bi-printer me-2"></i>Select Course
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Print by Batch -->
                            <div class="col-md-4">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-collection text-info" style="font-size: 2.5rem;"></i>
                                        </div>
                                        <h5 class="fw-bold">Print by Batch</h5>
                                        <p class="text-muted small">Print all approved documents for a specific batch</p>
                                        <button class="btn btn-info" onclick="showPrintByBatchModal()">
                                            <i class="bi bi-printer me-2"></i>Select Batch
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Individual Print Tab -->
                    <div class="tab-pane fade" id="individual-print" role="tabpanel">
                        <h5 class="fw-bold mb-3">Individual User Print</h5>
                        <p class="text-muted">Search and print documents for individual students.</p>

                        <!-- Search User -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Search Student</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="searchStudent" placeholder="Enter student name or ID...">
                                    <button class="btn btn-primary" onclick="searchStudent()">Search</button>
                                </div>
                            </div>
                        </div>

                        <!-- Student List -->
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAllStudents">
                                        </th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>School</th>
                                        <th>Course</th>
                                        <th>Batch</th>
                                        <th>Documents</th>
                                        <th>TOR</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input student-checkbox"></td>
                                        <td><span class="badge bg-secondary">2024-001</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">Juan Dela Cruz</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info">TB5</span></td>
                                        <td>CSS</td>
                                        <td>Batch 2024-01</td>
                                        <td><span class="badge bg-success">5 Approved</span></td>
                                        <td><span class="badge bg-primary">Available</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="printUserDocuments(1)">
                                                    <i class="bi bi-file-earmark"></i> Docs
                                                </button>
                                                <button class="btn btn-outline-success" onclick="printUserTOR(1)">
                                                    <i class="bi bi-clipboard-data"></i> TOR
                                                </button>
                                                <button class="btn btn-outline-info" onclick="printUserAll(1)">
                                                    <i class="bi bi-printer"></i> All
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input student-checkbox"></td>
                                        <td><span class="badge bg-secondary">2024-002</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">Maria Santos</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info">TB5</span></td>
                                        <td>BPP</td>
                                        <td>Batch 2024-01</td>
                                        <td><span class="badge bg-success">4 Approved</span></td>
                                        <td><span class="badge bg-primary">Available</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="printUserDocuments(2)">
                                                    <i class="bi bi-file-earmark"></i> Docs
                                                </button>
                                                <button class="btn btn-outline-success" onclick="printUserTOR(2)">
                                                    <i class="bi bi-clipboard-data"></i> TOR
                                                </button>
                                                <button class="btn btn-outline-info" onclick="printUserAll(2)">
                                                    <i class="bi bi-printer"></i> All
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><input type="checkbox" class="form-check-input student-checkbox"></td>
                                        <td><span class="badge bg-secondary">2024-003</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">Pedro Reyes</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-warning">BBI</span></td>
                                        <td>COK</td>
                                        <td>Batch 2024-02</td>
                                        <td><span class="badge bg-success">6 Approved</span></td>
                                        <td><span class="badge bg-primary">Available</span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="printUserDocuments(3)">
                                                    <i class="bi bi-file-earmark"></i> Docs
                                                </button>
                                                <button class="btn btn-outline-success" onclick="printUserTOR(3)">
                                                    <i class="bi bi-clipboard-data"></i> TOR
                                                </button>
                                                <button class="btn btn-outline-info" onclick="printUserAll(3)">
                                                    <i class="bi bi-printer"></i> All
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Batch Actions for Selected -->
                        <div class="card bg-light border-0 mt-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">
                                        <span id="selectedCount">0</span> student(s) selected
                                    </span>
                                    <div class="btn-group">
                                        <button class="btn btn-primary" onclick="printSelectedDocuments()">
                                            <i class="bi bi-file-earmark me-1"></i> Print Documents
                                        </button>
                                        <button class="btn btn-success" onclick="printSelectedTOR()">
                                            <i class="bi bi-clipboard-data me-1"></i> Print TOR
                                        </button>
                                        <button class="btn btn-info" onclick="printSelectedAll()">
                                            <i class="bi bi-printer me-1"></i> Print All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TOR Print Tab -->
                    <div class="tab-pane fade" id="tor-print" role="tabpanel">
                        <h5 class="fw-bold mb-3">TOR (Transcript of Records) Print</h5>
                        <p class="text-muted">Print TOR documents individually or in batch.</p>

                        <div class="row g-4">
                            <!-- TOR by School -->
                            <div class="col-md-6">
                                <div class="card border-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-building text-primary" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold">Print TOR by School</h5>
                                                <small class="text-muted">Generate TOR for all students in a school</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Select School</label>
                                            <select class="form-select" id="torSchoolSelect">
                                                <option value="">Choose School...</option>
                                                <option value="tb5">TB5</option>
                                                <option value="bbi">BBI</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="printTORBySchool()">
                                            <i class="bi bi-printer me-2"></i>Generate & Print TOR
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- TOR by Course -->
                            <div class="col-md-6">
                                <div class="card border-success h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-book text-success" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold">Print TOR by Course</h5>
                                                <small class="text-muted">Generate TOR for all students in a course</small>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Select School First</label>
                                            <select class="form-select mb-2" id="torCourseSchoolSelect" onchange="updateTORCourses()">
                                                <option value="">Choose School...</option>
                                                <option value="tb5">TB5</option>
                                                <option value="bbi">BBI</option>
                                            </select>
                                            <label class="form-label">Select Course</label>
                                            <select class="form-select" id="torCourseSelect" disabled>
                                                <option value="">Choose Course...</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-success w-100" onclick="printTORByCourse()">
                                            <i class="bi bi-printer me-2"></i>Generate & Print TOR
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- TOR by Batch -->
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                                <i class="bi bi-collection text-info" style="font-size: 1.5rem;"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-0 fw-bold">Print TOR by Batch</h5>
                                                <small class="text-muted">Generate TOR for all students in a specific batch</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Select Batch</label>
                                                <select class="form-select" id="torBatchSelect">
                                                    <option value="">Choose Batch...</option>
                                                    <option value="batch-2024-01">Batch 2024-01</option>
                                                    <option value="batch-2024-02">Batch 2024-02</option>
                                                    <option value="batch-2025-01">Batch 2025-01</option>
                                                    <option value="batch-2026-01">Batch 2026-01</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8 d-flex align-items-end">
                                                <button class="btn btn-info" onclick="printTORByBatch()">
                                                    <i class="bi bi-printer me-2"></i>Generate & Print TOR
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Print Jobs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-bold">Recent Print Jobs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Documents</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Feb 13, 2026 10:30 AM</td>
                                <td><span class="badge bg-primary">Batch Print</span></td>
                                <td>TB5 - CSS Course</td>
                                <td>45 Documents</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadPrintJob(1)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Feb 13, 2026 09:15 AM</td>
                                <td><span class="badge bg-success">TOR Print</span></td>
                                <td>Batch 2024-01</td>
                                <td>28 TOR</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadPrintJob(2)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>Feb 12, 2026 04:45 PM</td>
                                <td><span class="badge bg-info">Individual</span></td>
                                <td>Juan Dela Cruz</td>
                                <td>5 Documents + TOR</td>
                                <td><span class="badge bg-success">Completed</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadPrintJob(3)">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print by School Modal -->
<div class="modal fade" id="printBySchoolModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Documents by School</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select School</label>
                    <select class="form-select" id="batchPrintSchool">
                        <option value="">Choose School...</option>
                        <option value="tb5">TB5</option>
                        <option value="bbi">BBI</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Print Options</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeTORSchool" checked>
                        <label class="form-check-label" for="includeTORSchool">
                            Include TOR
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeDocsSchool" checked>
                        <label class="form-check-label" for="includeDocsSchool">
                            Include Approved Documents
                        </label>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    This will generate a PDF containing all approved documents for the selected school.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="confirmPrintBySchool()">
                    <i class="bi bi-printer me-2"></i>Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print by Course Modal -->
<div class="modal fade" id="printByCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Documents by Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select School</label>
                    <select class="form-select" id="batchPrintCourseSchool" onchange="updateCourseOptions()">
                        <option value="">Choose School...</option>
                        <option value="tb5">TB5</option>
                        <option value="bbi">BBI</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Course</label>
                    <select class="form-select" id="batchPrintCourse" disabled>
                        <option value="">Choose Course...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Print Options</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeTORCourse" checked>
                        <label class="form-check-label" for="includeTORCourse">
                            Include TOR
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeDocsCourse" checked>
                        <label class="form-check-label" for="includeDocsCourse">
                            Include Approved Documents
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmPrintByCourse()">
                    <i class="bi bi-printer me-2"></i>Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print by Batch Modal -->
<div class="modal fade" id="printByBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Print Documents by Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Batch</label>
                    <select class="form-select" id="batchPrintBatch">
                        <option value="">Choose Batch...</option>
                        <option value="batch-2024-01">Batch 2024-01</option>
                        <option value="batch-2024-02">Batch 2024-02</option>
                        <option value="batch-2025-01">Batch 2025-01</option>
                        <option value="batch-2026-01">Batch 2026-01</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Print Options</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeTORBatch" checked>
                        <label class="form-check-label" for="includeTORBatch">
                            Include TOR
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="includeDocsBatch" checked>
                        <label class="form-check-label" for="includeDocsBatch">
                            Include Approved Documents
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-info" onclick="confirmPrintByBatch()">
                    <i class="bi bi-printer me-2"></i>Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Course options for each school
const courseOptions = {
    tb5: [
        { value: 'css', text: 'CSS' },
        { value: 'bpp', text: 'BPP' },
        { value: 'hsk', text: 'HSK' },
        { value: 'epas', text: 'EPAS' },
        { value: 'tmi', text: 'TMI' }
    ],
    bbi: [
        { value: 'cok', text: 'COK' },
        { value: 'hsk', text: 'HSK' },
        { value: 'eim', text: 'EIM' },
        { value: 'fbs', text: 'FBS' },
        { value: 'evm', text: 'EVM' }
    ]
};

// Show modals
function showPrintBySchoolModal() {
    const modal = new bootstrap.Modal(document.getElementById('printBySchoolModal'));
    modal.show();
}

function showPrintByCourseModal() {
    const modal = new bootstrap.Modal(document.getElementById('printByCourseModal'));
    modal.show();
}

function showPrintByBatchModal() {
    const modal = new bootstrap.Modal(document.getElementById('printByBatchModal'));
    modal.show();
}

// Update course options based on school selection
function updateCourseOptions() {
    const school = document.getElementById('batchPrintCourseSchool').value;
    const courseDropdown = document.getElementById('batchPrintCourse');
    
    courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
    
    if (school && courseOptions[school]) {
        courseDropdown.disabled = false;
        courseOptions[school].forEach(course => {
            const option = document.createElement('option');
            option.value = course.value;
            option.textContent = course.text;
            courseDropdown.appendChild(option);
        });
    } else {
        courseDropdown.disabled = true;
    }
}

function updateTORCourses() {
    const school = document.getElementById('torCourseSchoolSelect').value;
    const courseDropdown = document.getElementById('torCourseSelect');
    
    courseDropdown.innerHTML = '<option value="">Choose Course...</option>';
    
    if (school && courseOptions[school]) {
        courseDropdown.disabled = false;
        courseOptions[school].forEach(course => {
            const option = document.createElement('option');
            option.value = course.value;
            option.textContent = course.text;
            courseDropdown.appendChild(option);
        });
    } else {
        courseDropdown.disabled = true;
    }
}

// Print functions
function confirmPrintBySchool() {
    const school = document.getElementById('batchPrintSchool').value;
    const includeTOR = document.getElementById('includeTORSchool').checked;
    const includeDocs = document.getElementById('includeDocsSchool').checked;
    
    if (!school) {
        alert('Please select a school');
        return;
    }
    
    alert(`Generating PDF for ${school.toUpperCase()}\nTOR: ${includeTOR ? 'Yes' : 'No'}\nDocuments: ${includeDocs ? 'Yes' : 'No'}`);
    bootstrap.Modal.getInstance(document.getElementById('printBySchoolModal')).hide();
    // Add your PDF generation logic here
}

function confirmPrintByCourse() {
    const school = document.getElementById('batchPrintCourseSchool').value;
    const course = document.getElementById('batchPrintCourse').value;
    const includeTOR = document.getElementById('includeTORCourse').checked;
    const includeDocs = document.getElementById('includeDocsCourse').checked;
    
    if (!school || !course) {
        alert('Please select both school and course');
        return;
    }
    
    alert(`Generating PDF for ${course.toUpperCase()} - ${school.toUpperCase()}\nTOR: ${includeTOR ? 'Yes' : 'No'}\nDocuments: ${includeDocs ? 'Yes' : 'No'}`);
    bootstrap.Modal.getInstance(document.getElementById('printByCourseModal')).hide();
    // Add your PDF generation logic here
}

function confirmPrintByBatch() {
    const batch = document.getElementById('batchPrintBatch').value;
    const includeTOR = document.getElementById('includeTORBatch').checked;
    const includeDocs = document.getElementById('includeDocsBatch').checked;
    
    if (!batch) {
        alert('Please select a batch');
        return;
    }
    
    alert(`Generating PDF for ${batch}\nTOR: ${includeTOR ? 'Yes' : 'No'}\nDocuments: ${includeDocs ? 'Yes' : 'No'}`);
    bootstrap.Modal.getInstance(document.getElementById('printByBatchModal')).hide();
    // Add your PDF generation logic here
}

// Individual print functions
function printUserDocuments(userId) {
    alert('Printing documents for user #' + userId);
    // Add your print logic here
}

function printUserTOR(userId) {
    alert('Printing TOR for user #' + userId);
    // Add your print logic here
}

function printUserAll(userId) {
    alert('Printing all documents and TOR for user #' + userId);
    // Add your print logic here
}

// TOR print functions
function printTORBySchool() {
    const school = document.getElementById('torSchoolSelect').value;
    if (!school) {
        alert('Please select a school');
        return;
    }
    alert('Generating TOR for ' + school.toUpperCase());
    // Add your print logic here
}

function printTORByCourse() {
    const school = document.getElementById('torCourseSchoolSelect').value;
    const course = document.getElementById('torCourseSelect').value;
    if (!school || !course) {
        alert('Please select both school and course');
        return;
    }
    alert('Generating TOR for ' + course.toUpperCase() + ' - ' + school.toUpperCase());
    // Add your print logic here
}

function printTORByBatch() {
    const batch = document.getElementById('torBatchSelect').value;
    if (!batch) {
        alert('Please select a batch');
        return;
    }
    alert('Generating TOR for ' + batch);
    // Add your print logic here
}

// Selection handling
document.getElementById('selectAllStudents').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateSelectedCount();
});

document.querySelectorAll('.student-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.student-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

function printSelectedDocuments() {
    const count = document.querySelectorAll('.student-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one student');
        return;
    }
    alert('Printing documents for ' + count + ' selected student(s)');
    // Add your print logic here
}

function printSelectedTOR() {
    const count = document.querySelectorAll('.student-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one student');
        return;
    }
    alert('Printing TOR for ' + count + ' selected student(s)');
    // Add your print logic here
}

function printSelectedAll() {
    const count = document.querySelectorAll('.student-checkbox:checked').length;
    if (count === 0) {
        alert('Please select at least one student');
        return;
    }
    alert('Printing all documents and TOR for ' + count + ' selected student(s)');
    // Add your print logic here
}

// Search function
function searchStudent() {
    const searchTerm = document.getElementById('searchStudent').value.toLowerCase();
    const tableRows = document.querySelectorAll('#studentTableBody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

// Download print job
function downloadPrintJob(jobId) {
    alert('Downloading print job #' + jobId);
    // Add your download logic here
}
</script>

<?php
// Include footer
include('../footer/footer.php');
?>