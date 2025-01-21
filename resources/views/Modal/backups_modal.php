<!-- Backups Modal -->
<div class="modal fade" id="backupsModal" tabindex="-1" aria-labelledby="backupsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header ">
                <h5 class="modal-title fw-bold" id="backupsModalLabel">
                    <i class="bi bi-cloud-arrow-up-fill"></i> Backups for File: <span id="modalFileName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 10%;">#</th>
                            <th scope="col" style="width: 40%;">Backup Date</th>
                            <th scope="col" style="width: 20%;">Backup</th>
                            <th scope="col" style="width: 20%;">Restore</th>
                        </tr>
                    </thead>
                    <tbody id="backupTableBody">


                    </tbody>
                </table>
            </div>
            <div class="modal-footer ">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
