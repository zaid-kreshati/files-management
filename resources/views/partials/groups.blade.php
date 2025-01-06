
<div id="groupd">
    @csrf

    <div class="split-screen">
        <div class="left">

    <!-- Create Group Modal -->
    <div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGroupModalLabel">Create Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createGroupForm">
                        <div class="mb-3">
                            <label for="groupName" class="form-label">Group Name:</label>
                            <input type="text" class="form-control" id="groupName" name="groupName" required>
                        </div>
                        <button type="submit" class="modal-btn">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Group Modal -->
    <div class="modal fade" id="editGroupModal" tabindex="-1" aria-labelledby="editGroupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupModalLabel">Edit Group Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGroupForm" method="PUT">
                        <div class="mb-3">
                            <label for="editGroupName" class="form-label">Group Name:</label>
                            <input type="text" class="form-control" id="editGroupName" name="groupName" required>
                        </div>
                        <button type="submit" id="editGroupBtn" class="modal-btn">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Cards Section -->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center" id="groupcontainer">
                @if(!is_null($groups))
                @foreach ($groups as $group)
                    <div class="col mb-5" id="group-{{ $group->id }}">
                        <div class="card h-100" data-group-id="{{ $group->id }}">
                            <div class="dropdown">
                                <img src="{{ asset('icons/three_dots_icon.png') }}" alt="three_dots_icon"
                                    class="three-dots-icon" id="dropdown-{{ $group->id }}" data-bs-toggle="dropdown"
                                    aria-expanded="false" data-id="{{ $group->id }}" data-name="{{ $group->name }}">

                                <ul class="dropdown-menu" aria-labelledby="dropdown-{{ $group->id }}">
                                    <li>
                                        <button class="dropdown-item edit-group-btn" data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-bs-toggle="modal" data-bs-target="#editGroupModal">
                                            Edit Group
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item delete-group-btn" data-id="{{ $group->id }}">
                                            Delete Group
                                        </button>
                                    </li>
                                </ul>
                            </div>

                            <!-- Group Image -->
                            <div class="group-image-container" style="cursor: pointer;">
                                <img class="card-img-top" src="{{ asset('/icons/group_of_file.png') }}" alt="group_icon"
                                    data-group-id="{{ $group->id }}" data-group-name="{{ $group->name }}" >
                            </div>

                            <!-- Group Name -->
                            <div class="card-body p-4">
                                <div class="text-center" id="group-name-{{ $group->id }}">
                                    <h5 class="fw-bolder">{{ $group->name }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
        </div>

        <!-- Loading Spinner -->
      <div id="loading-spinner" class="text-center" style="display: content;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
     </div>


    </section>

        </div>

        <div class="right container mt-4">
            <h2 class="text-center mb-4">Invitation Requests</h2>
            <div id="invitation-request-container">
                @if(!is_null($invitationRequests)&&count($invitationRequests)>0)
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark text-center">
                        <tr>
                            <th scope="col">From</th>
                            <th scope="col">Group</th>
                            <th scope="col">Accept</th>
                            <th scope="col">Reject</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invitationRequests as $invitationRequest)
                            <tr class="text-center align-middle" id="invitation-request-{{ $invitationRequest->id }}">
                                <td>{{ $invitationRequest->sender->name }}</td>
                                <td>{{ $invitationRequest->group->name }}</td>
                                <td>
                                    <button id="invitation-response-btn" class="btn btn-success btn-sm me-2 accept-btn"
                                    data-id="{{ $invitationRequest->id }}" data-response="accepted">Accept</button>
                                    </td>
                                <td>
                                    <button id="invitation-response-btn" class="btn btn-danger btn-sm reject-btn"
                                    data-id="{{ $invitationRequest->id }}" data-response="rejected">Reject</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>No invitation requests</p>
                @endif
            </div>
        </div>





</div>

<script src="{{ asset('assets/js/groups.js') }}"></script>
