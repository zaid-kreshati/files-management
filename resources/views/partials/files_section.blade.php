<section  id="files-section">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4" id="files-container">
            @if ($files)
                @foreach ($files as $file)
                    <div class="col mb-5" >
                        <div class="card h-100 file-card" id="file-{{ $file->id }}" style="cursor: pointer;"
                            data-file-id="{{ $file->id }}">
                            <div class="dropdown">
                                <img src="{{ asset('icons/three_dots_icon.png') }}" alt="three-dots-icon"
                                    class="three-dots-icon" id="dropdown-{{ $file->id }}" data-bs-toggle="dropdown"
                                    aria-expanded="false" data-id="{{ $file->id }}" data-name="{{ $file->name }}">

                                <ul class="dropdown-menu" aria-labelledby="dropdown-{{ $file->id }}">
                                    <li>
                                        <button class="dropdown-item delete-file-btn" data-id="{{ $file->id }}">
                                            Delete File
                                        </button>
                                    </li>
                                    <li>
                                        @if ($file->status == 'free')
                                           <a href="{{ route('download.file', $file->id) }}"
                                            class="dropdown-item download-file-success" data-file-id="{{ $file->id }}">
                                                Download File
                                            </a>
                                        @else
                                            <button class="dropdown-item download-file-error" data-file-id="{{ $file->id }}">
                                                Download File
                                            </button>
                                        @endif
                                    </li>
                                    <li>
                                        <a class="dropdown-item back-ups-file-btn" data-bs-toggle="modal"
                                            data-bs-target="#backupsModal" data-file="{{ json_encode($file) }}">
                                            Backups
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('audit.export', $file->id) }}" method="post">
                                            @csrf
                                            <input type="hidden" name="file_id" value="{{ $file->id }}">
                                            <input type="hidden" name="file_name" value="{{ $file->name }}">
                                            <button class="dropdown-item export-file-btn" type="submit">
                                                Export audit as PDF
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <!-- File image-->
                            <img class="file-icon" src="{{ asset('icons/file_icon.png') }}" alt="{{ $file->name }}"
                                data-file-id="{{ $file->id }}">

                            <!-- File details-->
                            <div class="text-center">
                                <!-- File name-->
                                <h5 class="fw-bolder">{{ $file->name }}</h5>
                            </div>

                            <div class="text-center">
                                <!-- File status-->
                                <h6>{{ 'Status: ' . $file->status }}</h6>
                            </div>

                            <!-- Checkbox for selecting the file -->
                            <div class="text-center">
                                <input type="checkbox" class="file-checkbox" data-file-id="{{ $file->id }}"
                                    data-file-name="{{ $file->name }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>





