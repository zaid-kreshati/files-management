<div id="filed" data-group-id="{{ $groupId }}" class="files">
    <div class="split-screen">
        <div class="left">
            <div class="files-header">
                <button id="uploadFileBtn" class="upload-file-btn" data-bs-toggle="modal"
                    data-bs-target="#uploadFileModal">Upload New File</button>

                    <button id="checkInFileBtn" class="check-in-file-btn" data-group-id="{{ $groupId }}">
                        Check In File
                    </button>

                    <button id="checkOutFileForm" class="check-out-file-btn"
                    data-group-id="{{ $groupId }}" >Check Out File</button>
            </div>
            <!-- upload file modal -->
            <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadFileModalLabel">Upload File</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="uploadFileForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="file" class="form-label">Choose a .txt file:</label>
                                    <input type="file" class="form-control" id="file" name="file" required
                                        accept=".txt">
                                </div>
                                <input type="hidden" name="groupId" id="groupId" data-group-id="{{ $groupId }}">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- files section -->
            @include('partials.files_section', ['files' => $files,'groupId'=>$groupId])
        </div>

        <div class="right">
            <!-- Dropdown for User Selection -->
            <div class="mb-3">
                <label for="userDropdown" class="form-label">Invite User:</label>
                <select id="userDropdown" name="user_id" class="form-select" style="width: 300px;">
                    <option></option> <!-- Placeholder for Select2 -->
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Member List -->
            @php
                $owner=true;
                $user=Auth::user();
                $name=$user->name;
            @endphp
            {{ $name }}
            <div class="member-list bg-light p-3" style="width: 300px; " id="member-list">
                <h5 class="text-center">Group Members</h5>
                <ul class="list-group">
                    @foreach ($group->members as $member)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <!-- Member Name on the Left -->
                        <span>{{ $member->name }}</span>

                        <!-- Membership on the Right -->
                        <span class="badge bg-secondary">
                            @if($owner)
                                Owner
                                @php
                                    $owner = false;
                                @endphp
                            @else
                                Member
                            @endif
                        </span>
                    </li>

                    @endforeach
                </ul>

            </div>


            <div class="container mt-4">
                <h2 class="text-center mb-4">Pending Files</h2>
                <div id="pending-file-container">
                    @if(!is_null($pendingFiles)&&count($pendingFiles)>0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-dark text-center">
                            <tr>
                                <th scope="col">File Name</th>
                                <th scope="col">Accept</th>
                                <th scope="col">Reject</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingFiles as $pendingFile)
                                <tr class="text-center align-middle" id="pending-file-{{ $pendingFile->id }}">
                                    <td>{{ $pendingFile->name }}</td>
                                    <td>
                                        <button id="pending-file-btn" class="btn btn-success btn-sm me-2 accept-btn"
                                        data-id="{{ $pendingFile->id }}" data-response="approved">Accept</button>
                                    </td>
                                    <td>
                                        <button id="pending-file-btn" class="btn btn-danger btn-sm reject-btn"
                                        data-id="{{ $pendingFile->id }}" data-response="rejected">Reject</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p>No pending files</p>
                    @endif
                </div>
            </div>


        </div>
    </div>
</div>
<script src="{{ asset('assets/js/files.js') }}"></script>
