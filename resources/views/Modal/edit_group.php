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
                        <button type="submit" id="editGroupBtn" class="btnall">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
