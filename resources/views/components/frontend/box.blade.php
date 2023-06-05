<style>
    .box-letter-spacing {
        letter-spacing: 0.7px;
        font-size: 15px;
    }

    .card-header-colour-none {
        background-color: #fafafa !important;
        background: linear-gradient(45deg, transparent 0%, transparent 100%) !important;
        box-shadow: none !important;
    }
</style>

<div class="mb-15">
    <div class="card bg-shahzad-lightgray">
        <div class="card-header card-header-colour-none">
            <span class="pt-3">
                <div class="row py-3">
                    <div class="col-md-12">
                        <span class="box-letter-spacing">{{ $title }}</span>
                    </div>
                </div>
            </span>
        </div>

        <div class="card-body bg-light-gray">
            {{ $slot }}
        </div>
    </div>
</div>


