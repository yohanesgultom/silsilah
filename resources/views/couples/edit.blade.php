<!--extend('layouts.app')-->

@section('content')
<h2 class="page-header">
     {{ $couple->husband->name }} & {{ $couple->wife->name }} <small>{{ trans('couple.edit') }}</small>
</h2>

@include('couples.partials.stat')

<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading"><h3 class="panel-title">{{ trans('couple.update') }}</h3></div>
            <form method="POST" action="{{ route('couples.update', $couple) }}" autocomplete="off">
                @csrf
                @method('PATCH')
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="marriage_date">{{ trans('couple.marriage_date') }}</label>
                                <input type="text" name="marriage_date" id="marriage_date" class="form-control" value="{{ old('marriage_date', $couple->marriage_date) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="divorce_date">{{ trans('couple.divorce_date') }}</label>
                                <input type="text" name="divorce_date" id="divorce_date" class="form-control" value="{{ old('divorce_date', $couple->divorce_date) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-success">{{ trans('couple.update') }}</button>
                    <a href="{{ route('couples.show', $couple) }}" class="btn btn-default">{{ trans('app.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section ('ext_css')
<link rel="stylesheet" href="{{ asset('css/plugins/jquery.datetimepicker.css') }}">
@endsection

@section ('ext_js')
<script src="{{ asset('js/plugins/jquery.datetimepicker.js') }}"></script>
@endsection

@section ('script')
<script>
(function () {
    $('#marriage_date, #divorce_date').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true,
        scrollInput: false
    });
})();
</script>
@endsection