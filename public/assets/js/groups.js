// Helper function to get the CSRF token
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}


// Function to create a group card dynamically
function createGroupCard(group) {
    return `
        <div class="col mb-5 responsive-group" id="group-${group.id}">
            <div class="card h-100" data-group-id="${group.id}">
                <div class="dropdown">
                    <img src="icons/three_dots_icon.png" alt="three-dots-icon"
                        class="three-dots-icon" id="dropdown-${group.id}" data-bs-toggle="dropdown"
                        aria-expanded="false" data-id="${group.id}" data-name="${group.name}">
                    <ul class="dropdown-menu" aria-labelledby="dropdown-${group.id}">
                        <li>
                            <button class="dropdown-item edit-group-btn" data-id="${group.id}" data-name="${group.name}">
                                Edit Group
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item delete-group-btn" data-id="${group.id}">
                                Delete Group
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Group Image -->
                <div class="group-image-container">
                    <img class="card-img-top" src="icons/group_of_file.png" alt="${group.name}"
                        data-group-id="${group.id}" data-group-name="${group.name}">
                </div>

                <div >
                        <h3 class="text-center" id="groupName-${group.id}" style="color:var(--text-color1);">${group.name}</h3>
                </div>

            </div>
        </div>`;
}



$('#createGroupForm').on('submit', function (e) {
    e.preventDefault();

    const submitButton = $(this).find('button[type="submit"]');
    submitButton.prop('disabled', true).text('Creating...');

    const groupName = $('#groupName').val();

    $.ajax({
        url: '/groups/store',
        method: 'POST',
        data: { name: groupName, _token: getCsrfToken() },
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    width: '70%',
                    title: 'Success',
                    text: 'Group created successfully',
                    icon: 'success'
                });

                $('#createGroupModal').modal('hide');
                $('#groupName').val('');

                $('#groupcontainer').append(createGroupCard(response.data.group));

            } else {
                Swal.fire({
                    width: '70%',
                    title: 'Error',
                    text: 'Failed to create group',
                    icon: 'error',
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', { status, error, xhr });
            Swal.fire({
                width: '70%',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                icon: 'error',
            });
        },
        complete: function () {
            submitButton.prop('disabled', false).text('Create Group');
        }
    });
});

$(document).on('click', '.edit-group-btn', function () {

    const groupId = $(this).data('id');
    const groupName = $(this).data('name');

    // Populate the modal form
    $('#editGroupForm').data('id', groupId);
    $('#editGroupName').val(groupName);


    $.ajax({
        url: `/groups/check-owner/${groupId}`,
        method: 'GET',
        success: function (response) {
            console.log(response);
            // Show the modal
            $('#editGroupModal').modal('show');
        },
        error: function (error) {
            console.log(error);

            // Show error with SweetAlert
            Swal.fire({
                width: '70%',
                title: 'Forbidden',
                text: error.responseJSON?.message || 'An unexpected error occurred.',
                icon: 'error',
            });
        }
    });
});


$('#editGroupForm').on('submit', function (e) {
    e.preventDefault();

    const submitButton = $(this).find('button[type="submit"]');
    submitButton.prop('disabled', true).text('Updating...');

    const groupId = $(this).data('id');
    const groupName = $('#editGroupName').val();


    $.ajax({
        url: `/groups/${groupId}/edit`,
        method: 'PUT',
        data: {
            name: groupName,
            _token: getCsrfToken()
        },
        success: function () {

            const groupElement = $(`#groupName-${groupId}`);
            console.log('groupElement',groupElement);
            if (groupElement.length > 0) {
                groupElement.text(groupName);
                console.log(`Updated group name to: ${groupName}`);
            } else {
                console.error(`Element with ID groupName-${groupId} not found.`);
            }

            $('#editGroupModal').modal('hide');
            $('#editGroupName').val('');


            Swal.fire({
                width: '70%',
                title: 'Success',
                text: 'Group updated successfully!',
                icon: 'success'
            });
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', { status, error, xhr });

            Swal.fire({
                width: '70%',
                title: 'Error',
                text: 'Something went wrong. Please try again.',
                icon: 'error',
            });
        },
        complete: function () {
            submitButton.prop('disabled', false).text('Save Changes');
        }
    });
});




// Delete group logic
$(document).on('click', '.delete-group-btn', function () {
    const groupId = $(this).data('id');
    Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the group permanently.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/groups/${groupId}/delete`,
                    method: 'DELETE',
                    data: { _token: getCsrfToken() },
                    success: function (response) {
                        if (response.success) {
                            $(`#group-${groupId}`).fadeOut('slow', function () {
                                $(this).remove();
                            });

                            Swal.fire({
                                width: '70%',
                                title: 'Success',
                                text: 'Group has been deleted.',
                                icon: 'success'
                            });
                        } else {
                            Swal.fire({
                                width: '70%',
                                title: 'Error',
                                text: 'Failed to delete group.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function (error) {
                        console.log(error);
                        Swal.fire({
                            width: '70%',
                            title: 'Error',
                            text: error.responseJSON.message,
                            icon: 'error',
                        });
                    },
                });
            }
        });
});


let page = 1; // Current page
let loading = false; // To prevent multiple requests
let hasMorePages = true; // To check if more pages are available
let isSearching = false; // Flag to manage search and pagination states

// Function to fetch and load groups
function loadMoreGroups() {
    if (isSearching || loading || !hasMorePages) return;
    loading = true;

    $('#loading-spinner').show();
    console.log(page);

    $.ajax({
        url: `/groups/allwithpagination`,
        method: 'POST',
        data: {
            page: page,
            _token: getCsrfToken()
        },
        success: function (response) {
            if (response.data.groups.data.length) {
                let html = '';
                response.data.groups.data.forEach(group => {
                    html += createGroupCard(group);
                });
                $('#groupcontainer').append(html);
                page++; // Increment the page
            } else {
                hasMorePages = false; // No more pages to load
            }
        },
        error: function () {
            console.error('An error occurred while fetching groups.');
        },
        complete: function () {
            loading = false;
            let timeout = setTimeout(() => {
                $('#loading-spinner').hide();
            }, 2000);
        }
    });
}

// Search functionality
(function () {
    const searchBox = $('#searchBox');
    const groupSection = $('#group-section');

    // Debounce function to limit the frequency of the search
    function debounce(func, wait) {
        let timeout;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }

    // Reset pagination variables
    function resetPagination() {
        page = 1;
        loading = false;
        hasMorePages = true;
    }

    searchBox.on('input',
        debounce(function () {
            const query = this.value.toLowerCase().trim();

            if (query === '') {
                isSearching = false;
                $('#groupcontainer').html('');
                resetPagination();
            } else {
                isSearching = true;
                resetPagination();

                $.ajax({
                    url: '/groups/search',
                    method: 'POST',
                    data: {
                        search: query,
                        _token: getCsrfToken()
                    },
                    beforeSend: function () {
                        $('#loading-spinner').show();
                    },
                    success: function (response) {
                        console.log(response.data);
                        $('#group-section').html(response.data);
                    },
                    error: function () {
                        alert('An error occurred while searching. Please try again.');
                    },
                    complete: function () {
                        $('#loading-spinner').hide();
                    }
                });
            }
        }, 300)
    );
})();

$(document).on('click', '.card-img-top', function () {
    const groupId = $(this).data('group-id');
    handleGroupClick(groupId);
});
// Handle group clicks
function handleGroupClick(groupId) {
    console.log('Group clicked:', groupId);

    if (groupId) {
        $.ajax({
            url: `/files/${groupId}/all`,
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            success: function (response) {
                console.log('Group details:', response);
                window.location.href = `/files/${groupId}/all`;
                // Handle navigation or display details here
            },
            error: function (xhr, status, error) {
                console.error('Failed to get group details:', error);
            },
        });
    } else {
        console.error('Invalid group ID:', groupId);
    }
}


// Debounce function to limit the frequency of scroll event handling
function debounce(func, wait) {
    let timeout;
    return function () {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// Attach infinite scroll handler
$(document).ready(function () {
    function attachScrollHandler() {
        $(window).scroll(
            debounce(() => {
                if (!hasMorePages || isSearching) return; // Stop scrolling if no more pages or searching
                if ($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
                    loadMoreGroups();
                }
            }, 250)
        );
    }

    loadMoreGroups(); // Load the first page
    attachScrollHandler(); // Attach the scroll handler
});

$(document).on('click', '#invitation-response-btn', function () {
    const invitationId = $(this).data('id');
    const response = $(this).data('response');
    console.log('Invitation ID:', invitationId);
    $.ajax({
        url: `/group/invitations/${invitationId}/respond`,
        method: 'POST',
        data: {
            response: response,
            _token: getCsrfToken()
        },
        success: function (response) {
            if (response.success) {
                console.log(response);
                if (response.data.status === 'accepted') {
                    $('#groupcontainer').append(createGroupCard(response.data.group));

                    Swal.fire({
                        width: '70%',
                        title: 'Success',
                        text: response.message,
                        icon: 'success'
                    });
                } else {
                    Swal.fire({
                        width: '70%',
                        title: 'Rejected',
                        text: response.message,
                        icon: 'error'
                    });
                }

                $(`#invitation-request-${invitationId}`).remove();
                if($('#invitation-request-container').children().length==0){
                    $('#invitation-request-container').hide();
                }
            }
        },
    });
});





