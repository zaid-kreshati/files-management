// Helper function to get the CSRF token
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// document.getElementById('uploadFileBtn').onclick = function() {
    $('#uploadFileBtn').on('click', function() {
    console.log("modal");
    $('#uploadFileModal').modal('show');
});

document.getElementById('uploadFileForm').addEventListener('submit', function(event) {

    event.preventDefault();
    const file = document.getElementById('file').files[0];
    const formData = new FormData();
    formData.append('file', file);
    const groupId = document.getElementById('groupId').getAttribute('data-group-id');
    $.ajax({
        url: `/files/${groupId}/upload`, // Updated URL to include the group id
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        },
        contentType: false,
        processData: false,
        success: function(response) {
            if (response.success) {
                console.log(response);
                Swal.fire({
                    title: 'Success',
                    text: 'File uploaded successfully',
                    icon: 'success'
                });
                document.getElementById('uploadFileModal').style.display = 'none';
                $('#uploadFileModal').modal('hide');
                // Append the new file to the index.blade.php
                const newFile = document.createElement('div');
                newFile.innerHTML = `<div class="col mb-5">
                                        <div class="card h-100" id="file-${response.data.id}" style="cursor: pointer;"
                                        data-file-id="${response.data.id}">

                                        <div class="dropdown">
                                             <img src="/icons/three_dots_icon.png" alt="three-dots-icon"
                                                class="three-dots-icon" id="dropdown-${response.data.id}" data-bs-toggle="dropdown"
                                                aria-expanded="false" data-id="${response.data.id}" data-name="${response.data.name}">

                                            <ul class="dropdown-menu" aria-labelledby="dropdown-${response.data.id}">
                                                <li>
                                                    <button class="dropdown-item edit-file-btn" data-id="${response.data.id}" data-name="${response.data.name}" data-bs-toggle="modal" data-bs-target="#editFileModal">
                                                        Edit File
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item delete-file-btn" data-id="${response.data.id}">
                                                        Delete File
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>

                                            <!-- File image-->
                                            <img class="file-icon" src="/icons/file_icon.png"
                                            alt="${response.data.name}" data-file-id="${response.data.id}" >

                                            <!-- File details-->
                                            <div class="text-center">
                                                    <!-- File name-->
                                                    <h5 class="fw-bolder">${response.data.name}</h5>
                                                </div>


                                            <div class="text-center">
                                                <!-- File status-->
                                                <h6>Status: ${response.data.status}</h6>
                                            </div>


                                             <!-- Checkbox for selecting the file -->
                                            <div class="text-center">
                                                <input type="checkbox" class="file-checkbox"
                                                data-file-id="${response.data.id}" data-file-name="${response.data.name}">
                                            </div>

                                        </div>
                                    </div>`;

                if(response.data.approval_status === 'approved'){
                    document.querySelector('.row').appendChild(newFile);
                }
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to upload file',
                    icon: 'error'
                });
            }
            console.log('File uploaded:', response);
            $('#file').val('');
        }
    });
    document.getElementById('uploadFileModal').style.display = 'none';
});



$(document).ready(function () {
    // Initialize Select2
    $('#userDropdown').select2({
        placeholder: "Select a user",
        allowClear: true,
        width: '300px',
    });

    // Handle user selection and send invite automatically
    $('#userDropdown').on('change', function () {
        const userId = $(this).val(); // Get selected user ID
        const groupId = $('#filed').data('group-id'); // Get group ID

        if (!userId) return; // Exit if no user is selected

        $.ajax({
            url: `/group/inviteuser`, // Backend route for inviting user
            method: 'POST',
            data: {
                user_id: userId,
                group_id: groupId,
                _token: getCsrfToken(), // CSRF token
            },
            success: function (response) {
                console.log(response);
                if (response.success) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Invitation sent successfully to ' + response.data + '!!',
                        icon: 'success',
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message || 'Failed to send the invitation.',
                        icon: 'error',
                    });
                }
                $('#userDropdown').val(null).trigger('change'); // Reset dropdown
            },
            error: function () {
                $('#userDropdown').val(null).trigger('change'); // Reset dropdown
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while sending the invitation.',
                    icon: 'error',
                });
            },
        });
    });
});

 // Delete file logic
 $(document).on('click', '.delete-file-btn', function () {
    const fileId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the file permanently.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/files/${fileId}/delete`,
                method: 'DELETE',
                data: { _token: getCsrfToken() },
                success: function (response) {
                    if (response.success) {
                        $(`#file-${fileId}`).fadeOut('slow', function () {
                            $(this).remove();
                        });

                        Swal.fire('Deleted!', 'File has been deleted.', 'success');
                    } else {
                        Swal.fire('Error', 'Failed to delete file.', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', { status, error, xhr });
                    Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                },
            });
        }
    });
});

$(document).on('click', '.file-icon', function () {
    const fileId = $(this).data('file-id');
    handleFileClick(fileId);
});
// Handle group clicks
function handleFileClick(fileId) {
    console.log('File clicked:', fileId);

    if (fileId) {
        // Redirect the user to download/preview the file
        window.location.href = `/files/${fileId}/open`;
    } else {
        console.error('Invalid file ID:', fileId);
    }

}

$(document).on('click', '#pending-file-btn', function () {
    const fileId = $(this).data('id');
    const approvalStatus = $(this).data('response');
    console.log('File ID:', fileId);
    console.log('Response:', approvalStatus);
    $.ajax({
        url: `/files/${fileId}/respond`,
        method: 'POST',
        data: {
            approval_status: approvalStatus,
            _token: getCsrfToken()
        },
        success: function (response) {
            if(response.success){
                console.log(response);
                $(`#pending-file-${fileId}`).remove();
                if(approvalStatus === 'approved'){
                    $(`#file-${fileId}`).append(createFileCard(response.data.file));
                }
                Swal.fire('Success', response.message, 'success');
            }else{
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
});

    // Store selected file IDs
    let selectedFiles = [];
    let selectedFilesName = [];
    // Handle file checkbox toggle
    $(document).on('change', '.file-checkbox', function () {
        const fileId = $(this).data('file-id');
        const fileName = $(this).data('file-name');
        console.log('File ID:', fileId);
        if ($(this).is(':checked')) {
            // Add file to the selection
            if (!selectedFiles.includes(fileId)) {
                selectedFiles.push(fileId);
                selectedFilesName.push(fileName);
            }
        } else {
            // Remove file from the selection
            selectedFiles = selectedFiles.filter(id => id !== fileId);
            selectedFilesName = selectedFilesName.filter(name => name !== fileName);
        }
    });

    // Handle Check-In Button Click
    $('#checkInFileBtn').on('click', function () {
        console.log('Selected Files:', selectedFiles);
        const groupId = $('#checkInFileBtn').data('group-id');
        console.log('Group ID:', groupId);
        if (selectedFiles.length === 0) {
            Swal.fire('No Files Selected', 'Please select at least one file to check in.', 'warning');
            return;
        }

        // Send selected files to the server for check-in
        $.ajax({
            url: '/files/check-in',
            method: 'POST',
            data: {
                file_ids: selectedFiles,
                _token: getCsrfToken(),
                group_id: groupId
            },
            success: function (response) {
                Swal.fire('Success', 'Files checked in successfully!', 'success');
                console.log(response);
                $('#files-section').html(response.data);
                //location.reload(); // Reload the page to reflect changes
            },
            error: function (error) {
                console.error(error);
                Swal.fire('Error', 'An error occurred while checking in files.', 'error');
            }
        });
    });

    // Handle Check-Out Button Click
    $('#checkOutFileForm').on('click', function (event) {
        event.preventDefault();

        const groupId = $('#checkOutFileForm').data('group-id'); // Retrieve the group ID
        selectedFilesName.forEach(fileName => {
            console.log(fileName);
        });


        // // Send selected files to the server for check-out
        $.ajax({
            url: `/files/check-out/${groupId}/file`,
            method: 'POST',
            data:{
                files_names:selectedFilesName,
                _token:getCsrfToken()
            } ,
            success: function (response) {
                Swal.fire('Success', 'Files checked out successfully!', 'success');
                $('#files-section').html(response.data);

            },
            error: function (error) {
                console.error(error);
                Swal.fire('Error', 'An error occurred while checking out files.', 'error');
            }
        });
    });

    $(document).on('click', '.download-file-btn', function () {
        const fileId = $(this).data('file-id');
        console.log('File ID:', fileId);
        $.ajax({
            url: `/files/download/${fileId}`,
            method: 'GET',
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.error(error);
                Swal.fire('Error', 'An error occurred while downloading the file.', 'error');
            }
        });
    });







