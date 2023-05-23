<div class="modal fade in" id="modal-confirm">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="" method="get" name="confirm_form">
                <!-- Modal Header -->
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">
                        {{ __('custom.attention') }}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <p></p>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">{{ __('custom.yes') }}</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('custom.no') }}</button>
                </div>
            </form>

        </div>
    </div>
</div>
