<section class="quick-view">
    <div class="modal fade" id="{{$keyId}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" style="top:20%">
            <div>
                <div class="modal-content" id="changePresidentModal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{$title}}</h5>
                        <a href="javascript:void(0)" data-bs-dismiss="modal" aria-label="Close">X</a>
                    </div>
                    <div class="quick-veiw-area">
                        <div class="px-4 pt-3 pb-3">
                            {{$slot}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
