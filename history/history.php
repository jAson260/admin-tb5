<?php
session_start();
require_once('../includes/rbac-guard.php');
checkStudent();

include '../includes/header.php';
include '../includes/sidebar.php';

require_once('../db-connect.php');

$historyData = [];
try {
    $stmt = $pdo->prepare("
        SELECT 
            e.Id,
            c.CourseName,
            c.CourseCode,
            b.BatchCode,
            b.BatchName,
            b.StartDate,
            b.EndDate,
            e.School,
            e.Status,
            e.EnrolledAt,
            e.Remarks
        FROM enrollments e
        INNER JOIN courses c ON e.CourseId = c.Id
        INNER JOIN batches b ON e.BatchId = b.Id
        WHERE e.StudentId = ?
        ORDER BY e.EnrolledAt DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $historyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Enrollment History Error: ' . $e->getMessage());
}
?>

<div class="main-content">
    <div class="container-fluid">

        <div class="row mb-4 align-items-end">
            <div class="col-md-5">
                <h3 class="fw-bold"><i class="fas fa-history me-2 text-primary"></i>Enrollment History</h3>
                <p class="text-muted small">Search by keyword or specific application date.</p>
            </div>

            <!-- SEARCH CONTROLS -->
            <div class="col-md-7">
                <div class="row g-2">
                    <div class="col-md-7">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="historySearch" class="form-control border-start-0 ps-0" placeholder="Search course or batch...">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-day text-muted"></i></span>
                            <input type="date" id="dateSearch" class="form-control border-start-0 ps-0">
                            <button class="btn btn-outline-secondary border-start-0" type="button" onclick="clearDateFilter()" title="Clear Date">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-royal">My Course Enrollments</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Sort By
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="#" onclick="sortTable(3, 'date')">Date Enrolled (Newest)</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable(0, 'string')">Course Name (A-Z)</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable(4, 'string')">Status</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="historyTable" data-sort-dir="asc">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width:30%; cursor:pointer;" onclick="sortTable(0, 'string')">
                                    Course / Qualification <i class="fas fa-sort ms-1 small text-muted"></i>
                                </th>
                                <th style="width:20%;">Batch</th>
                                <th style="width:10%;">School</th>
                                <th style="width:20%; cursor:pointer;" onclick="sortTable(3, 'date')">
                                    Date Enrolled <i class="fas fa-sort ms-1 small text-muted"></i>
                                </th>
                                <th class="text-center" style="width:20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <?php if (empty($historyData)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x" style="font-size:3rem;"></i>
                                        <p class="mt-2 mb-0">No enrollment records found</p>
                                        <small>Please wait for admin to assign you to a course</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($historyData as $row):
                                    $enrolledDate    = $row['EnrolledAt'] ? date('Y-m-d', strtotime($row['EnrolledAt'])) : '';
                                    $enrolledDisplay = $enrolledDate ? date('M d, Y', strtotime($enrolledDate)) : 'N/A';
                                    $startDisplay    = $row['StartDate'] ? date('M d, Y', strtotime($row['StartDate'])) : 'TBA';
                                    $endDisplay      = $row['EndDate']   ? date('M d, Y', strtotime($row['EndDate']))   : 'TBA';

                                    $badge = match($row['Status']) {
                                        'Enrolled'  => 'bg-success text-white',
                                        'Ongoing'   => 'bg-primary text-white',
                                        'Completed' => 'bg-success text-white',
                                        'Dropped'   => 'bg-danger text-white',
                                        'Failed'    => 'bg-danger text-white',
                                        default     => 'bg-warning text-dark'
                                    };
                                ?>
                                <tr class="history-row" data-date="<?php echo $enrolledDate; ?>">
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark course-name">
                                            <?php echo htmlspecialchars($row['CourseName']); ?>
                                        </div>
                                        <small class="text-muted">
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($row['CourseCode']); ?></span>
                                        </small>
                                    </td>
                                    <td class="batch-name">
                                        <div class="fw-semibold"><?php echo htmlspecialchars($row['BatchCode']); ?> - <?php echo htmlspecialchars($row['BatchName']); ?></div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-range me-1"></i>
                                            <?php echo $startDisplay; ?> – <?php echo $endDisplay; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                            $schoolBadge = $row['School'] === 'TB5'
                                                ? 'bg-info text-white'
                                                : 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?php echo $schoolBadge; ?>">
                                            <?php echo htmlspecialchars($row['School']); ?>
                                        </span>
                                    </td>
                                    <td data-order="<?php echo $enrolledDate; ?>">
                                        <i class="far fa-calendar-alt me-1 text-secondary"></i>
                                        <?php echo $enrolledDisplay; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill shadow-sm">
                                            <?php echo htmlspecialchars($row['Status']); ?>
                                        </span>
                                        <?php if (!empty($row['Remarks'])): ?>
                                            <div class="mt-2 text-start mx-auto" style="max-width:220px;">
                                                <div class="p-2 border border-secondary-subtle bg-secondary bg-opacity-10 rounded text-muted" style="font-size:11px; font-style:italic;">
                                                    "<?php echo htmlspecialchars($row['Remarks']); ?>"
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <small class="text-muted" id="recordCount">Total Records: <?php echo count($historyData); ?></small>
            </div>
        </div>
    </div>
</div>

<script>
    const textSearch         = document.getElementById('historySearch');
    const dateSearch         = document.getElementById('dateSearch');
    const rows               = document.querySelectorAll('.history-row');
    const recordCountDisplay = document.getElementById('recordCount');

    function applyFilters() {
        let textFilter   = textSearch.value.toLowerCase();
        let dateFilter   = dateSearch.value;
        let visibleCount = 0;

        rows.forEach(row => {
            let course  = row.querySelector('.course-name').textContent.toLowerCase();
            let batch   = row.querySelector('.batch-name').textContent.toLowerCase();
            let rowDate = row.getAttribute('data-date');

            let matchesText = course.includes(textFilter) || batch.includes(textFilter);
            let matchesDate = dateFilter === "" || rowDate === dateFilter;

            if (matchesText && matchesDate) {
                row.style.display = "";
                visibleCount++;
            } else {
                row.style.display = "none";
            }
        });
        recordCountDisplay.textContent = "Found: " + visibleCount + " record(s)";
    }

    textSearch.addEventListener('keyup', applyFilters);
    dateSearch.addEventListener('change', applyFilters);

    function clearDateFilter() {
        dateSearch.value = "";
        applyFilters();
    }

    function sortTable(columnIndex, type) {
        let table       = document.getElementById("historyTable");
        let rowsArray   = Array.from(document.querySelectorAll('.history-row'));
        let isAscending = table.getAttribute("data-sort-dir") === "asc";

        rowsArray.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim();
            let valB = b.cells[columnIndex].innerText.trim();

            if (type === 'date') {
                valA = a.cells[columnIndex].getAttribute('data-order') || '';
                valB = b.cells[columnIndex].getAttribute('data-order') || '';
            }

            return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });

        let tbody = document.getElementById("historyTableBody");
        rowsArray.forEach(row => tbody.appendChild(row));
        table.setAttribute("data-sort-dir", isAscending ? "desc" : "asc");
    }
</script>

<?php include '../includes/footer.php'; ?>