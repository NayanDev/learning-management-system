<div class="modal fade" tabindex="-1" role="dialog" id="modalMultiLink">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Navigations</h5>
            </div>
            <div class="modal-body">
                <div class="row multilink-section">
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function setMM(el) {
        var links = $(el).data('links')

        var mHtml = ""
        $.each(links, function( index, link ) {
            mHtml += "<div class='col-md-6'>";
            if (link.disabled) {
                mHtml += "<a href='javascript:void(0);' class='btn btn-outline-secondary w-100 my-1 text-left disabled-link' target='_blank'>";
            } else {
                mHtml += "<a href='"+link.url+"' class='btn btn-outline-secondary w-100 my-1 text-left' target='_blank'>";
            }
            mHtml += "<i class='"+link.icon+" fw-bold'></i><br>";
            mHtml += link.label;
            mHtml += "</a>";
            mHtml += "</div>";
        })

        $(".multilink-section").html(mHtml)
    }
</script>