<?php

session_start();
require_once('../../includes/rbac-guard.php');
checkAdmin();

// Include header
include('../header/header.php');
include('../sidebar/sidebar.php');
?>

<div class="content-wrapper">
    <div class="main-content">
        <div class="container-fluid">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="bi bi-file-earmark-text me-2"></i>Transcript of Records & Grades
                    </h2>
                    <p class="text-muted mb-0">Generate and manage student TOR/Certificates</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateTORModal">
                    <i class="bi bi-plus-circle me-2"></i>Generate TOR
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-check text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">Total TORs</h6>
                                    <h4 class="mb-0 fw-bold" id="totalTORs">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center">
                                        <i class="bi bi-award text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">Competent</h6>
                                    <h4 class="mb-0 fw-bold text-success" id="competentCount">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center">
                                        <i class="bi bi-calendar-check text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">This Month</h6>
                                    <h4 class="mb-0 fw-bold text-info" id="thisMonthCount">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center">
                                        <i class="bi bi-download text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">Downloads</h6>
                                    <h4 class="mb-0 fw-bold text-warning" id="downloadCount">0</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TOR Records Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2"></i>TOR Records
                    </h5>
                </div>
                <div class="card-body">
                    <table id="torTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Course</th>
                                <th>Date of Graduation</th>
                                <th>ISO Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate TOR Modal -->
<div class="modal fade" id="generateTORModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white">
                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Generate TOR CSV
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="generateTORForm">
                    <div class="row g-3">
                        <!-- Student Selection -->
                        <div class="col-md-12">
                            <label for="studentSelect" class="form-label">Select Student <span class="text-danger">*</span></label>
                            <select class="form-select" id="studentSelect" name="student_id" required>
                                <option value="">Loading students...</option>
                            </select>
                            <div id="studentInfo" class="mt-2 text-muted small"></div>
                        </div>

                        <!-- Course Selection -->
                        <div class="col-md-12">
                            <label for="courseSelect" class="form-label">Course/Qualification <span class="text-muted">(Optional)</span></label>
                            <select class="form-select" id="courseSelect" name="course_id">
                                <option value="">Select student first</option>
                            </select>
                            <div class="form-text">If not selected, TOR will be generated for all enrolled courses</div>
                        </div>

                        <!-- Grades Section -->
                        <div class="col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">
                                        <i class="bi bi-clipboard-data me-2"></i>Student Grades
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="theoreticalGrade" class="form-label">Theoretical Grade (%) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="theoreticalGrade" name="theoretical_grade" 
                                                   min="0" max="100" step="0.01" required placeholder="e.g., 95.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="practicalGrade" class="form-label">Practical Grade (%) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="practicalGrade" name="practical_grade" 
                                                   min="0" max="100" step="0.01" required placeholder="e.g., 94.00">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="averageGrade" class="form-label">Average Grade (%) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="averageGrade" name="average_grade" 
                                                   min="0" max="100" step="0.01" required placeholder="Auto-calculated">
                                            <div class="form-text">Will auto-calculate from theoretical and practical</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="finalGrade" class="form-label">Final Grade (%) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="finalGrade" name="final_grade" 
                                                   min="0" max="100" step="0.01" required placeholder="Auto-calculated">
                                            <div class="form-text">Same as average grade</div>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="remarks" class="form-label">Remarks <span class="text-danger">*</span></label>
                                            <select class="form-select" id="remarks" name="remarks" required>
                                                <option value="">Select Remarks</option>
                                                <option value="Competent" selected>Competent</option>
                                                <option value="Not Yet Competent">Not Yet Competent</option>
                                                <option value="Incomplete">Incomplete</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date of Graduation -->
                        <div class="col-md-6">
                            <label for="graduationDate" class="form-label">Date of Graduation <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="graduationDate" name="graduation_date" required>
                        </div>

                        <!-- ISO Number -->
                        <div class="col-md-6">
                            <label for="isoNumber" class="form-label">ISO Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="isoNumber" name="iso_number" required 
                                   placeholder="e.g., 0434-1VETBM111-0322-2025">
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Please ensure all grades and information are accurate before generating the CSV file.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="submitGenerateTOR">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Generate CSV
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    const torTable = $('#torTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'get-tor-records.php',
            type: 'POST',
            error: function(xhr, error, code) {
                console.error('DataTable Error:', error);
                console.error('Response:', xhr.responseText);
            }
        },
        columns: [
            { data: 'student_id' },
            { data: 'student_name' },
            { data: 'course_name' },
            { data: 'graduation_date' },
            { data: 'iso_number' },
            { 
                data: 'status',
                render: function(data) {
                    const badges = {
                        'Competent': 'success',
                        'Not Yet Competent': 'danger',
                        'Pending': 'warning'
                    };
                    return `<span class="badge bg-${badges[data] || 'secondary'}">${data}</span>`;
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-success" onclick="downloadTOR(${data.id})" title="Download CSV">
                                <i class="bi bi-download"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTOR(${data.id})" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[3, 'desc']],
        pageLength: 25
    });

    // Load students for dropdown
    loadStudents();

    // Load statistics
    loadStatistics();

    // Student change event
    $('#studentSelect').on('change', function() {
        const studentId = $(this).val();
        if (studentId) {
            loadStudentCourses(studentId);
            loadStudentInfo(studentId);
        }
    });

    // Auto-calculate grades
    $('#theoreticalGrade, #practicalGrade').on('input', function() {
        const theoretical = parseFloat($('#theoreticalGrade').val()) || 0;
        const practical = parseFloat($('#practicalGrade').val()) || 0;
        
        if (theoretical > 0 && practical > 0) {
            const average = (theoretical + practical) / 2;
            $('#averageGrade').val(average.toFixed(2));
            $('#finalGrade').val(average.toFixed(2));
            
            // Auto-set remarks based on grade
            if (average >= 75) {
                $('#remarks').val('Competent');
            } else {
                $('#remarks').val('Not Yet Competent');
            }
        }
    });

    // Generate TOR submission - CSV VERSION
    $('#submitGenerateTOR').on('click', function() {
        const form = $('#generateTORForm')[0];
        
        const studentId = $('#studentSelect').val();
        const theoreticalGrade = $('#theoreticalGrade').val();
        const practicalGrade = $('#practicalGrade').val();
        const graduationDate = $('#graduationDate').val();
        const isoNumber = $('#isoNumber').val();
        
        if (!studentId) {
            alert('Please select a student');
            return;
        }
        
        if (!theoreticalGrade || !practicalGrade || !graduationDate || !isoNumber) {
            form.reportValidity();
            return;
        }

        const courseId = $('#courseSelect').val();
        
        const formData = {
            student_id: studentId,
            course_id: courseId || null,  // Can be null
            graduation_date: graduationDate,
            iso_number: isoNumber,
            theoretical_grade: theoreticalGrade,
            practical_grade: practicalGrade,
            average_grade: $('#averageGrade').val(),
            final_grade: $('#finalGrade').val(),
            remarks: $('#remarks').val()
        };

        console.log('Submitting data:', formData); // Debug

        const submitBtn = $(this);
        const originalHtml = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Generating CSV...');

        // Use AJAX to submit and then trigger download
        $.ajax({
            url: 'generate-tor-csv.php',
            method: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            xhrFields: {
            responseType: 'blob' // Important for file download
        },
        success: function(blob, status, xhr) {
            // Create a download link
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            
            // Get filename from Content-Disposition header or use default
            const disposition = xhr.getResponseHeader('Content-Disposition');
            let filename = 'TOR_' + Date.now() + '.csv';
            if (disposition && disposition.indexOf('filename=') !== -1) {
                const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            // Re-enable button and close modal
            submitBtn.prop('disabled', false).html(originalHtml);
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('generateTORModal'));
            modal.hide();
            
            // Refresh table and stats
            $('#torTable').DataTable().ajax.reload();
            loadStatistics();
            
            // Reset form
            form.reset();
        },
        error: function(xhr, status, error) {
            console.error('Error generating CSV:', error);
            console.error('Response:', xhr.responseText);
            alert('Failed to generate CSV. Check console for details.');
            submitBtn.prop('disabled', false).html(originalHtml);
        }
    });
    });

    // Reset form when modal is closed
    $('#generateTORModal').on('hidden.bs.modal', function() {
        $('#generateTORForm')[0].reset();
        $('#studentInfo').html('');
        $('#courseSelect').html('<option value="">Select student first</option>');
    });
});

function loadStudents() {
    console.log('Loading students...');
    $.ajax({
        url: 'get-students.php',
        method: 'GET',
        dataType: 'json',
        success: function(students) {
            console.log('Students loaded:', students);
            const select = $('#studentSelect');
            select.empty().append('<option value="">Select Student</option>');
            
            if (Array.isArray(students) && students.length > 0) {
                students.forEach(student => {
                    select.append(`<option value="${student.id}">${student.name} (${student.uli})</option>`);
                });
            } else {
                select.append('<option value="">No students found</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading students:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            $('#studentSelect').html('<option value="">Error loading students - Check console</option>');
        }
    });
}

function loadStudentInfo(studentId) {
    console.log('Loading student info for ID:', studentId);
    $.ajax({
        url: 'get-student-info.php',
        method: 'GET',
        data: { student_id: studentId },
        dataType: 'json',
        success: function(info) {
            console.log('Student info loaded:', info);
            $('#studentInfo').html(`
                <strong>Email:</strong> ${info.email || 'N/A'} | 
                <strong>Contact:</strong> ${info.contact || 'N/A'}
            `);
        },
        error: function(xhr, status, error) {
            console.error('Error loading student info:', error);
            console.error('Response:', xhr.responseText);
            $('#studentInfo').html('<span class="text-danger">Failed to load student info</span>');
        }
    });
}

function loadStudentCourses(studentId) {
    console.log('Loading courses for student ID:', studentId);
    const select = $('#courseSelect');
    select.html('<option value="">Loading courses...</option>');
    
    $.ajax({
        url: 'get-student-courses.php',
        method: 'GET',
        data: { student_id: studentId },
        dataType: 'json',
        success: function(courses) {
            console.log('Courses loaded:', courses);
            select.empty().append('<option value="">Select Course</option>');
            
            if (Array.isArray(courses) && courses.length > 0) {
                courses.forEach(course => {
                    select.append(`<option value="${course.id}" data-enrollment="${course.enrollment_id}">${course.name}</option>`);
                });
            } else {
                select.append('<option value="">No courses found for this student</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading courses:', error);
            console.error('Response:', xhr.responseText);
            select.html('<option value="">Error loading courses - Check console</option>');
        }
    });
}

function loadStatistics() {
    console.log('Loading statistics...');
    $.ajax({
        url: 'get-tor-statistics.php',
        method: 'GET',
        dataType: 'json',
        success: function(stats) {
            console.log('Statistics loaded:', stats);
            $('#totalTORs').text(stats.total || 0);
            $('#competentCount').text(stats.competent || 0);
            $('#thisMonthCount').text(stats.this_month || 0);
            $('#downloadCount').text(stats.downloads || 0);
        },
        error: function(xhr, status, error) {
            console.error('Error loading statistics:', error);
            console.error('Response:', xhr.responseText);
            // Set to 0 if error
            $('#totalTORs').text(0);
            $('#competentCount').text(0);
            $('#thisMonthCount').text(0);
            $('#downloadCount').text(0);
        }
    });
}

function downloadTOR(id) {
    window.location.href = `download-tor-csv.php?id=${id}`;
}

function deleteTOR(id) {
    if (confirm('Are you sure you want to delete this TOR record?')) {
        $.post('delete-tor.php', { id: id }, function(response) {
            if (response.success) {
                alert('TOR deleted successfully!');
                $('#torTable').DataTable().ajax.reload();
                loadStatistics();
            } else {
                alert('Error: ' + response.message);
            }
        }, 'json').fail(function(xhr) {
            console.error('Delete error:', xhr.responseText);
            alert('Failed to delete TOR');
        });
    }
}
</script>

<?php
// Include footer
include('../footer/footer.php');
?>