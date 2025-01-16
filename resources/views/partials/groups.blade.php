<div id="groupd">
    @csrf
    <div class="split-screen">
        <div class="left">
            <!-- Search Input Field -->
            <div class="search-container">
                <input type="text" id="searchBox" autocomplete="off">
                <img src="{{ asset('icons/search_icon.png') }}" alt="search_icon" class="search-icon">
            </div>
            <button id="createGroupBtn" class="btnall create-group-btn" data-bs-toggle="modal"
                data-bs-target="#createGroupModal">Create New Group</button>

            <!-- Group Cards Section -->
            <section class="py-5 ">
                <div class="container px-4 px-lg-5 mt-5">
                    <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center"
                        id="groupcontainer">
                        @if (!is_null($groups))
                            @foreach ($groups as $group)
                                <div class="col mb-5" id="group-{{ $group->id }}">
                                    <div class="card h-100" data-group-id="{{ $group->id }}">
                                        <div class="dropdown">
                                            <img src="{{ asset('icons/three_dots_icon.png') }}" alt="three_dots_icon"
                                                class="three-dots-icon" id="dropdown-{{ $group->id }}"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                data-id="{{ $group->id }}" data-name="{{ $group->name }}">

                                            <ul class="dropdown-menu" aria-labelledby="dropdown-{{ $group->id }}">
                                                <li>
                                                    <button class="dropdown-item edit-group-btn"
                                                        data-id="{{ $group->id }}" data-name="{{ $group->name }}"
                                                        data-bs-toggle="modal" data-bs-target="#editGroupModal">
                                                        Edit Group
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item delete-group-btn"
                                                        data-id="{{ $group->id }}">
                                                        Delete Group
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Group Image -->
                                        <div class="group-image-container" style="cursor: pointer;">
                                            <img class="card-img-top" src="{{ asset('/icons/group_of_file.png') }}"
                                                alt="group_icon" data-group-id="{{ $group->id }}"
                                                data-group-name="{{ $group->name }}">
                                        </div>

                                        <!-- Group Name -->
                                        <div class="text-center" id="group-name-{{ $group->id }}">
                                                <h3>{{ $group->name }}</h3>
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

        <!-- Invitation Requests Section -->
        <div class="right container">
            <h2 class="text-center" style="color: #000000;;">Invitation Requests:</h2>
            <div id="invitation-request-container">
                    <table class="table table-bordered  table-hover">
                        <thead class="table-head text-center">
                            <tr>
                                <th scope="col">From</th>
                                <th scope="col">Group</th>
                                <th scope="col">Accept</th>
                                <th scope="col">Reject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!is_null($invitationRequests) && count($invitationRequests) > 0)
                            @foreach ($invitationRequests as $invitationRequest)
                                <tr class="table-body align-middle"
                                    id="invitation-request-{{ $invitationRequest->id }}">
                                    <td>{{ $invitationRequest->sender->name }}</td>
                                    <td>{{ $invitationRequest->group->name }}</td>
                                    <td>
                                        <span id="invitation-response-btn" class="true-icon accept-btn"
                                            data-id="{{ $invitationRequest->id }}" data-response="accepted">
                                            &#10003;</span> <!-- Checkmark -->
                                    </td>
                                    <td>
                                        <span id="invitation-response-btn" class="false-icon reject-btn"
                                            data-id="{{ $invitationRequest->id }}" data-response="rejected">
                                            &#10007;</span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center" style="background-color: #f8f9fa;">
                                    <h6>not found</h6>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    @include('Modal.create_group')
    @include('Modal.edit_group')
    <script src="{{ asset('assets/js/groups.js') }}"></script>
</div>
