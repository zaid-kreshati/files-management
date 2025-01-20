<div >
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
            @include('partials.group_section')
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
