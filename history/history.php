<?php 
include '../includes/header.php'; 
include '../includes/sidebar.php'; 

// SAMPLE DATA
$historyData = [
    ["course" => "Shielded Metal Arc Welding (SMAW) NC II", "batch" => "Batch 2023-01", "date" => "2023-01-15", "status" => "Approved", "reason" => ""],
    ["course" => "Caregiving NC II", "batch" => "Batch 2023-05", "date" => "2023-05-10", "status" => "Pending", "reason" => ""],
    ["course" => "EPAS NC II", "batch" => "Batch 2023-08", "date" => "2023-08-22", "status" => "Rejected", "reason" => "Incomplete documentary requirements."],
    ["course" => "Bookkeeping NC III", "batch" => "Batch 2024-01", "date" => "2024-01-05", "status" => "Approved", "reason" => ""]
];
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
                    <!-- Text Search -->
                    <div class="col-md-7">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="historySearch" class="form-control border-start-0 ps-0" placeholder="Search course or batch...">
                        </div>
                    </div>
                    <!-- Date Search -->
                    <div class="col-md-5">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar-day text-muted"></i></span>
                            <input type="date" id="dateSearch" class="form-control border-start-0 ps-0">
                            <!-- Clear Date Button -->
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
                <h5 class="mb-0 fw-bold text-royal">My Course Applications</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-1"></i> Sort By
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="#" onclick="sortTable(2, 'date')">Date Applied (Newest)</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable(0, 'string')">Course Name (A-Z)</a></li>
                        <li><a class="dropdown-item" href="#" onclick="sortTable(3, 'string')">Status</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="historyTable" data-sort-dir="asc">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 40%; cursor:pointer;" onclick="sortTable(0, 'string')">Course / Qualification <i class="fas fa-sort ms-1 small text-muted"></i></th>
                                <th style="width: 20%;">Batch</th>
                                <th style="width: 20%; cursor:pointer;" onclick="sortTable(2, 'date')">Date Applied <i class="fas fa-sort ms-1 small text-muted"></i></th>
                                <th class="text-center" style="width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <?php foreach ($historyData as $row): ?>
                                <tr class="history-row" data-date="<?php echo $row['date']; ?>">
                                    <td class="ps-4">
                                        <div class="text-dark fw-bold course-name"><?php echo $row['course']; ?></div>
                                    </td>
                                    <td class="batch-name">
                                        <span class="text-muted"><?php echo $row['batch']; ?></span>
                                    </td>
                                    <td data-order="<?php echo $row['date']; ?>">
                                        <i class="far fa-calendar-alt me-1 text-secondary"></i>
                                        <?php echo date('M d, Y', strtotime($row['date'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $badge = "bg-warning text-dark"; 
                                            if ($row['status'] == "Approved") $badge = "bg-success text-white";
                                            if ($row['status'] == "Rejected") $badge = "bg-danger text-white";
                                        ?>
                                        <span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill shadow-sm">
                                            <?php echo $row['status']; ?>
                                        </span>

                                        <?php if ($row['status'] == "Rejected"): ?>
                                            <div class="mt-2 text-start mx-auto" style="max-width: 220px;">
                                                <div class="p-2 border border-danger-subtle bg-danger bg-opacity-10 rounded text-danger" style="font-size: 11px; font-style: italic;">
                                                    "<?php echo $row['reason']; ?>"
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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

<!-- SEARCH, DATE FILTER & SORT SCRIPT -->
<script>
    const textSearch = document.getElementById('historySearch');
    const dateSearch = document.getElementById('dateSearch');
    const rows = document.querySelectorAll('.history-row');
    const recordCountDisplay = document.getElementById('recordCount');

    // Combined Filter Logic (Text + Date)
    function applyFilters() {
        let textFilter = textSearch.value.toLowerCase();
        let dateFilter = dateSearch.value; // Format: YYYY-MM-DD
        let visibleCount = 0;

        rows.forEach(row => {
            let course = row.querySelector('.course-name').textContent.toLowerCase();
            let batch = row.querySelector('.batch-name').textContent.toLowerCase();
            let rowDate = row.getAttribute('data-date'); // YYYY-MM-DD

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

    // Event Listeners
    textSearch.addEventListener('keyup', applyFilters);
    dateSearch.addEventListener('change', applyFilters);

    function clearDateFilter() {
        dateSearch.value = "";
        applyFilters();
    }

    // Sort Logic (Remains high performance)
    function sortTable(columnIndex, type) {
        let table = document.getElementById("historyTable");
        let rowsArray = Array.from(document.querySelectorAll('.history-row'));
        let isAscending = table.getAttribute("data-sort-dir") === "asc";

        rowsArray.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim();
            let valB = b.cells[columnIndex].innerText.trim();

            if (type === 'date') {
                valA = a.cells[columnIndex].getAttribute('data-order');
                valB = b.cells[columnIndex].getAttribute('data-order');
            }

            return isAscending ? valA.localeCompare(valB) : valB.localeCompare(valA);
        });

        let tbody = document.getElementById("historyTableBody");
        rowsArray.forEach(row => tbody.appendChild(row));
        table.setAttribute("data-sort-dir", isAscending ? "desc" : "asc");
    }
</script>

<?php include '../includes/footer.php'; ?>