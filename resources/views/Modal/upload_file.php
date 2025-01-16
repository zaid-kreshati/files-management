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
