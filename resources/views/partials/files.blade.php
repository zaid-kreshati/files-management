<div id="filed" data-group-id="{{ $groupId }}" class="files">
    @csrf
    <div class="split-screen">
        <!-- left section -->
        <div class="left">
            <div class="files-header">
                <button id="uploadFileBtn" class="btnall" data-bs-toggle="modal"
                    data-bs-target="#uploadFileModal" data-group-id="{{ $groupId }}">{{ __('Upload New File') }}</button>

                <button id="checkInFileBtn" class="btnall" data-group-id="{{ $groupId }}">
                    {{ __('Check In File') }}
                </button>
                <button id="checkOutFileForm" class="btnall"
                    data-group-id="{{ $groupId }}" >{{ __('Check Out File') }}</button>
            </div>

            <!-- files section -->
            @include('partials.files_section', ['files' => $files,'groupId'=>$groupId])
        </div>

        <div class="right">
            <!-- Dropdown for User Selection -->
            <div class="mb-3">
                <label for="userDropdown" class="form-label">{{ __('Invite User:') }}</label>
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
                <h5 class="text-center">{{ __('Group Members') }}</h5>
                <ul class="list-group">
                    @foreach ($group->members as $index => $member)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <!-- Member Name on the Left -->
                            <span>{{ $member->name }}</span>

                            <!-- Membership on the Right -->
                            <span class="badge bg-secondary">
                                @if($index === 0)
                                    {{ __('Owner') }}
                                @else
                                    {{ __('Member') }}
                                @endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Pending Files -->
            @if ($owner)
            <div class="container mt-4" style="margin-bottom: 100px;" id="pending-file-container">
                <h2 class="text-center" style="color: #000;">{{ __('Pending Files') }}</h2>
                <div id="pending-file-container">
                    @if(!is_null($pendingFiles)&&count($pendingFiles)>0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-head text-center">
                            <tr>
                                <th scope="col">{{ __('File Name') }}</th>
                                <th scope="col">{{ __('Accept') }}</th>
                                <th scope="col">{{ __('Reject') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingFiles as $pendingFile)
                                <tr class="text-center align-middle" style="background-color: #ffffff;" id="pending-file-{{ $pendingFile->id }}">
                                    <td>{{ $pendingFile->name }}</td>
                                    <td>

                                        <span id="pending-file-btn" class="true-icon accept-btn" style="cursor: pointer;"
                                            data-id="{{ $pendingFile->id }}" data-response="approved">
                                            &#10003;</span> <!-- Checkmark -->
                                    </td>
                                    <td>

                                        <span id="pending-file-btn" class="false-icon reject-btn" style="cursor: pointer;"
                                        data-id="{{ $pendingFile->id }}" data-response="rejected">
                                        &#10007;</span>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <h4 style="color: black; background-color: #ffffff">{{ __('No pending files') }}</h4>
                    @endif
                </div>
            </div>
            @else
            <h1 style="color: black; background-color: #ffffff">{{ $owner }}</h1>
            @endif

        </div>
    </div>

    @include('Modal.upload_file')
    @include('Modal.backups_modal')

    <script src="{{ asset('assets/js/files.js') }}"></script>
</div>
