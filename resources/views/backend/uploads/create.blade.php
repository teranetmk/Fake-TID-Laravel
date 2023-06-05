@extends('backend.layouts.default')

@section('content')

    <div class="k-content__head	k-grid__item">
        <div class="k-content__head-main">
            <h3 class="k-content__head-title">{{ __('backend/tids.title') }}</h3>
            <div class="k-content__head-breadcrumbs">
                <a href="#" class="k-content__head-breadcrumb-home"><i class="flaticon-home-2"></i></a>
                <span class="k-content__head-breadcrumb-separator"></span>
                <a href="{{ route('admin.uploads.index') }}"
                   class="k-content__head-breadcrumb-link">{{ __('backend/tids.title') }}</a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.uploads.store') }}" method="POST" enctype="multipart/form-data">

        @csrf

        <div class="form-group">
            <label for="product_id">{{ __('backend/tids.select_product') }}</label>
            <select name="product_id" id="product_id" class="form-control select2" style="min-width: 100px">

                @foreach($products as $product)

                    <option value="{{ $product->id }}">{{ $product->id }}: {{ $product->name }}</option>

                @endforeach

            </select>
        </div>

        <div class="form-group">
            <label for="file_loc">Land der Dateien</label>
            <select name="file_loc" id="file_loc" class="form-control select2" style="min-width: 100px">
                <option value="de">Deutschland</option>
                <option value="eu">Andere Länder</option>
            </select>
        </div>

        <div class="form-group">
            <label for="exampleFormControlFile1">{{ __('backend/tids.file_input') }}</label>
            <input type="file" name="tid_file[]" id="file" class="form-control-file" multiple/>
        </div>

        <button type="submit" class="btn btn-wide btn-bold btn-danger">{{ __('backend/tids.upload') }}</button>

    </form>

@endsection

@section('page_scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $(".btn-success").click(function () {
                var lsthmtl = $(".clone").html();
                $(".increment").after(lsthmtl);
            });
            $("body").on("click", ".btn-danger", function () {
                $(this).parents(".hdtuto.control-group.lst").remove();
            });
        });
    </script>
@endsection
