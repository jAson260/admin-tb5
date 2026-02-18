<?php include '../../includes/header.php'; ?>

<style>
    .main-content { margin-left: 0 !important; padding-top: 100px !important; padding-left: 80px !important; padding-right: 80px !important; }
    .text-blossom { color: #2e51b8 !important; }
    .btn-blossom { background-color: #2e51b8 !important; color: white !important; }
    .upload-box { border: 2px dashed #2e51b8; padding: 20px; border-radius: 10px; transition: 0.3s; }
    .upload-box:hover { border-color: #2e51b8; background: #8c9ced; }
</style>

<div class="main-content">
    <div class="container-fluid" style="max-width: 900px;">
        <h3 class="fw-bold text-dark"><i class="fas fa-file-upload me-2 text-blossom"></i>Upload Requirements</h3>
        <p class="text-muted small mb-4">Final Step for Big Blossom Institute Inc.</p>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form enctype="multipart/form-data">
                    <div class="row g-4">
                        <div class="col-md-6"><label class="small fw-bold">PSA Birth Certificate</label><div class="upload-box"><input type="file" class="form-control border-0"></div></div>
                        <div class="col-md-6"><label class="small fw-bold">TOR</label><div class="upload-box"><input type="file" class="form-control border-0"></div></div>
                        <div class="col-md-6"><label class="small fw-bold">Diploma</label><div class="upload-box"><input type="file" class="form-control border-0"></div></div>
                        <div class="col-md-6"><label class="small fw-bold">Marriage Cert (If Applicable)</label><div class="upload-box"><input type="file" class="form-control border-0"></div></div>
                         <label class="form-check-label small" for="bbPrivacy">
                           </div>
                           
          <!-- Instruction Note -->
                    <div class="mt-5 p-3 rounded-3 bg-light border-start border-primary border-4">
                        <small class="text-muted d-block">
                            <i class="fas fa-info-circle me-1 text-primary"></i> 
                            Please ensure files are not larger than 5MB each. Supported formats: <b>.pdf, .jpg, .png</b>
                        </small>
                    </div>
                    <!-- Privacy Check -->
                     
                    <div class="form-check d-flex align-items-start p-3 rounded-3 border shadow-sm" style="background-color: #f0f4ff; border-left: 5px solid var(--royal-blue) !important;">
                        
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="bbPrivacy">
                        <label class="form-check-label small" for="bbPrivacy">
                            I agree to the Terms and Privacy Policy of <strong>Big Blossom Institute Inc.</strong> My data will be used for verification only.
                        </label>
                    </div>

                    <div class="mt-5 d-flex justify-content-between">
                        <a href="bb_enrollment.php" class="btn btn-light rounded-pill px-4">Back</a>
                        <button type="button" id="bbComplete" class="btn btn-blossom rounded-pill px-5 py-3 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#bbSuccess" disabled>Complete Enrollment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="bbSuccess" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-5 rounded-5 border-0 shadow-lg">
            <i class="fas fa-check-circle text-success mb-4" style="font-size: 70px;"></i>
            <h3 class="fw-bold">Sent to Big Blossom!</h3>
            <p class="text-muted">Wait for admin approval. You will be redirected now.</p>
            <button onclick="window.location.href='../../dashboard/dashboard.php'" class="btn btn-blossom rounded-pill px-5">OK</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('bbPrivacy').addEventListener('change', function() {
        document.getElementById('bbComplete').disabled = !this.checked;
    });
</script>

<?php include '../../includes/footer.php'; ?>