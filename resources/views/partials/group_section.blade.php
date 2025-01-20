  <!-- Group Cards Section -->
  <section class="py-5 " id="group-section">
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
                            <div class="text-center" id="group-name-{{ $group->id }}" >
                                    <h3 style="color: black">{{ $group->name }}</h3>
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
