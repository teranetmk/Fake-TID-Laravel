<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><b id="productnametittle"></b></h6>
                <button type="button" class="close btn-close-icon" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="productdescription">
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function showdescription(id) {
        $.ajax({
            type: "GET",
            url: "{{ url('showdescription') }}/" + id,
            success: function (resp) {
                $('#productnametittle').html(resp.name);
                $('#productdescription').html(resp.descrption);
                $('#exampleModal').modal('show');
            }
        });
    }
</script>
