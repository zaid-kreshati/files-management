// Helper function to get the CSRF token
function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");
}

// document.getElementById('uploadFileBtn').onclick = function() {
$("#uploadFileBtn").on("click", function () {
    console.log("modal");
    $("#uploadFileModal").modal("show");
});

function createFileCard(file) {
    console.log(file);
    // Append the new file to the index.blade.php
    const newFile = document.createElement("div");
    newFile.innerHTML = `<div class="col mb-5">
                             <div class="card h-100" id="file-${file.id}" style="cursor: pointer;"
                             data-file-id="${file.id}">

                             <div class="dropdown">
                                  <img src="/icons/three_dots_icon.png" alt="three-dots-icon"
                                     class="three-dots-icon" id="dropdown-${file.id}" data-bs-toggle="dropdown"
                                     aria-expanded="false" data-id="${file.id}" data-name="${file.name}">

                                 <ul class="dropdown-menu" aria-labelledby="dropdown-${file.id}">

                                     <li>
                                     <button class="dropdown-item delete-file-btn" data-id="${file.id}">
                                             Delete File
                                         </button>
                                     </li>

                                      <li>
                                        <div>
                                            <a  class="dropdown-item download-file-btn" data-file-id="${file.id}">
                                                Download File
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item back-ups-file-btn" data-bs-toggle="modal"
                                            data-bs-target="#backupsModal" data-file="${file}">
                                            Backups
                                        </a>
                                    </li>


                                 </ul>
                             </div>

                                 <!-- File image-->
                                 <img class="file-icon" src="/icons/file_icon.png"
                                 alt="${file.name}" data-file-id="${file.id}" >

                                 <!-- File details-->
                                 <div class="text-center">
                                         <!-- File name-->
                                         <h5 class="fw-bolder">${file.name}</h5>
                                     </div>


                                 <div class="text-center">
                                     <!-- File status-->
                                     <h6>Status: ${file.status}</h6>
                                 </div>


                                  <!-- Checkbox for selecting the file -->
                                 <div class="text-center">
                                     <input type="checkbox" class="file-checkbox"
                                     data-file-id="${file.id}" data-file-name="${file.name}">
                                 </div>

                             </div>
                         </div>`;
    return newFile;
}

document
    .getElementById("uploadFileForm")
    .addEventListener("submit", function (event) {
        event.preventDefault();
        const file = document.getElementById("file").files[0];
        const formData = new FormData();
        formData.append("file", file);
        const groupId = $("#uploadFileBtn").data("group-id");
        $.ajax({
            url: `/files/${groupId}/upload`, // Updated URL to include the group id
            method: "POST",
            data: formData,
            headers: {
                "X-CSRF-TOKEN": getCsrfToken(),
            },
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    console.log(response);
                    Swal.fire({
                        width: "70%",
                        title: "Success",
                        text: response.message,
                        icon: "success",
                    });
                    document.getElementById("uploadFileModal").style.display =
                        "none";
                    $("#uploadFileModal").modal("hide");

                    if (response.data.approval_status === "approved") {
                        console.log(response.data);
                        uploadedFile = createFileCard(response.data);
                        $("#files-container").append(uploadedFile);
                    }
                } else {
                    Swal.fire({
                        width: "70%",
                        title: "Error",
                        text: "Failed to upload file",
                        icon: "error",
                    });
                }
                console.log("File uploaded:", response);
                $("#file").val("");
            },
        });
        document.getElementById("uploadFileModal").style.display = "none";
    });

$(document).ready(function () {
    // Initialize Select2
    $("#userDropdown").select2({
        placeholder: "Select a user",
        allowClear: true,
        width: "300px",
    });

    // Handle user selection and send invite automatically
    $("#userDropdown").on("change", function () {
        const userId = $(this).val(); // Get selected user ID
        const groupId = $("#filed").data("group-id"); // Get group ID

        if (!userId) return; // Exit if no user is selected

        $.ajax({
            url: `/group/inviteuser`, // Backend route for inviting user
            method: "POST",
            data: {
                user_id: userId,
                group_id: groupId,
                _token: getCsrfToken(), // CSRF token
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        width: "70%",
                        title: "Success",
                        text:
                            "Invitation sent successfully to " +
                            response.data +
                            "!!",
                        icon: "success",
                    });
                } else {
                    Swal.fire({
                        width: "70%",
                        title: "Error",
                        text:
                            response.message ||
                            "Failed to send the invitation.",
                        icon: "error",
                    });
                }
                $("#userDropdown").val(null).trigger("change"); // Reset dropdown
            },
            error: function () {
                $("#userDropdown").val(null).trigger("change"); // Reset dropdown
                Swal.fire({
                    width: "70%",
                    title: "Error",
                    text: "An error occurred while sending the invitation.",
                    icon: "error",
                });
            },
        });
    });
});

// Delete file logic
$(document).on("click", ".delete-file-btn", function () {
    const fileId = $(this).data("id");

    Swal.fire({
        width: "70%",
        title: "Are you sure?",
        text: "This will delete the file permanently.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/files/${fileId}/delete`,
                method: "DELETE",
                data: { _token: getCsrfToken() },
                success: function (response) {
                    if (response.success) {
                        $(`#file-${fileId}`).fadeOut("slow", function () {
                            $(this).remove();
                        });

                        Swal.fire({
                            width: "70%",
                            title: "Deleted!",
                            text: "File has been deleted.",
                            icon: "success",
                        });
                    } else {
                        Swal.fire({
                            width: "70%",
                            title: "Error",
                            text: "Failed to delete file.",
                            icon: "error",
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", { status, error, xhr });
                    Swal.fire({
                        width: "70%",
                        title: "Error",
                        text: "Something went wrong. Please try again.",
                        icon: "error",
                    });
                },
            });
        }
    });
});

$(document).on("click", ".file-icon", function () {
    const fileId = $(this).data("file-id");
    handleFileClick(fileId);
});
// Handle group clicks
function handleFileClick(fileId) {
    console.log("File clicked:", fileId);

    $.ajax({
        url: `/files/${fileId}/open`,
        method: "GET",
        success: function (response) {
            console.log(response);
            window.location.href = `/files/${fileId}/open`;
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                width: "70%",
                title: "Error",
                text: "An error occurred while opening the file.",
                icon: "error",
            });
        },
    });
}

$(document).on("click", "#pending-file-btn", function () {
    const fileId = $(this).data("id");
    const approvalStatus = $(this).data("response");
    console.log("File ID:", fileId);
    console.log("Response:", approvalStatus);
    $.ajax({
        url: `/files/${fileId}/respond`,
        method: "POST",
        data: {
            approval_status: approvalStatus,
            _token: getCsrfToken(),
        },
        success: function (response) {
            if (response.success) {
                console.log(response);
                $(`#pending-file-${fileId}`).remove();
                if (approvalStatus === "approved") {
                    $(`#file-${fileId}`).append(
                        createFileCard(response.data.file)
                    );
                }
                Swal.fire({
                    width: "70%",
                    title: "Success",
                    text: response.message,
                    icon: "success",
                });
            } else {
                Swal.fire({
                    width: "70%",
                    title: "Error",
                    text: response.message,
                    icon: "error",
                });
            }
        },
    });
});

// Store selected file IDs
let selectedFiles = [];
let selectedFilesName = [];
// Handle file checkbox toggle
$(document).on("change", ".file-checkbox", function () {
    const fileId = $(this).data("file-id");
    const fileName = $(this).data("file-name");
    console.log("File ID:", fileId);
    if ($(this).is(":checked")) {
        // Add file to the selection
        if (!selectedFiles.includes(fileId)) {
            selectedFiles.push(fileId);
            selectedFilesName.push(fileName);
        }
    } else {
        // Remove file from the selection
        selectedFiles = selectedFiles.filter((id) => id !== fileId);
        selectedFilesName = selectedFilesName.filter(
            (name) => name !== fileName
        );
    }
});

// Handle Check-In Button Click
$("#checkInFileBtn").on("click", function () {
    console.log("Selected Files:", selectedFiles);
    const groupId = $("#checkInFileBtn").data("group-id");
    console.log("Group ID:", groupId);
    if (selectedFiles.length === 0) {
        Swal.fire({
            width: "70%",
            title: "No Files Selected",
            text: "Please select at least one file to check in.",
            icon: "warning",
        });
        return;
    }

    // Send selected files to the server for check-in
    $.ajax({
        url: "/files/check-in",
        method: "POST",
        data: {
            file_ids: selectedFiles,
            _token: getCsrfToken(),
            group_id: groupId,
        },
        success: function (response) {
            Swal.fire({
                width: "70%",
                title: "Success",
                text: "Files checked in successfully!",
                icon: "success",
            });
            console.log(response);
            $("#files-section").html(response.data);
            //location.reload(); // Reload the page to reflect changes
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                width: "70%",
                title: "Error",
                text: "An error occurred while checking in files.",
                icon: "error",
            });
        },
    });
});

// Handle Check-Out Button Click
$("#checkOutFileForm").on("click", function (event) {
    event.preventDefault();

    const groupId = $("#checkOutFileForm").data("group-id"); // Retrieve the group ID
    selectedFilesName.forEach((fileName) => {
        console.log(fileName);
    });

    // // Send selected files to the server for check-out
    $.ajax({
        url: `/files/check-out/${groupId}/file`,
        method: "POST",
        data: {
            files_names: selectedFilesName,
            _token: getCsrfToken(),
        },
        success: function (response) {
            Swal.fire({
                width: "70%",
                title: "Success",
                text: "Files checked out successfully!",
                icon: "success",
            });
            $("#files-section").html(response.data);
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                width: "70%",
                title: "Error",
                text: error.responseJSON.message,
                icon: "error",
            });
        },
    });
});

$(document).on("click", ".download-file-btn", function () {
    const fileId = $(this).data("file-id");
    console.log("File ID:", fileId);
    $.ajax({
        url: `/files/download/${fileId}`,
        method: "GET",
        success: function (response) {
            console.log(response);
            Swal.fire({
                width: "70%",
                title: "Success",
                text: "The file is downloading",
                icon: "success",
            });
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                width: "70%",
                title: "Error",
                text: "the file is not free",
                icon: "error",
            });
        },
    });
});

$(document).on("click", ".back-ups-file-btn", function () {
    const file = $(this).data("file");
    console.log(file);
    $("#backupsModal").modal("show");
    $("#modalFileName").text(file.name);
    $("#modalFileId").text(file.id);

    const backupTableBody = document.getElementById("backupTableBody");
    backupTableBody.innerHTML = ""; // Clear any existing rows

    // Loop through the backups relation and add rows to the table
    if (file.backups.length > 0) {
        file.backups.forEach((backup, index) => {
            const row = document.createElement("tr");

        row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${new Date(backup.created_at).toLocaleString()}</td>
                    <td> <img src="/icons/backup-icon.webp" alt="backup-icon"
                     class="backup-icon" data-backup-id="${backup.id}" data-path="${backup.backup_path}"> </td>
                    <td>
                        <button type="button" class="btnall restore-backup-btn"
                         data-backup-path="${backup.backup_path}"
                         data-file-path="${file.path}">Restore</button>
                    </td>
                `;

            backupTableBody.appendChild(row);
        });
    } else {
        backupTableBody.innerHTML = '<tr><td colspan="3">No backups available</td></tr>';
    }
});

$(document).on("click", ".backup-icon", function () {
    const backupId = $(this).data("backup-id");
    const path = $(this).data("path");
    console.log("Backup ID:", backupId);
    console.log("Path:", path);

    $.ajax({
        url: `/files/open-backup/${backupId}`,
        method: "GET",
        data: {
            path: path,
            _token: getCsrfToken(),
        },
        success: function (response) {
            console.log(response);
        },
    });
});

$(document).on("click", ".restore-backup-btn", function () {
    const backupPath = $(this).data("backup-path");
    const filePath = $(this).data("file-path");
    console.log("Backup Path:", backupPath);
    console.log("File Path:", filePath);
    $.ajax({
        url: `/files/restore-backup`,
        method: "POST",
        data: {
            backup_path: backupPath,
            file_path: filePath,
            _token: getCsrfToken(),
        },
        success: function (response) {
            $("#backupsModal").modal("hide");
            Swal.fire({
                width: "70%",
                title: "Success",
                text: "Backup restored successfully",
                icon: "success",
            });
        },
        error: function (error) {
                Swal.fire({
                width: "70%",
                title: "Error",
                text: "An error occurred while restoring the backup",
                icon: "error",
            });
        },
    });
});
