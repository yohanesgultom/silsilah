@extends('layouts.app')

@section('content')
<h2 class="page-header">
    {{ trans('app.search_your_family') }}
    @if (request('q'))
    <small class="pull-right">{!! trans('app.user_found', ['total' => $users->total(), 'keyword' => request('q')]) !!}</small>
    @endif
</h2>


<form method="get" class="">
<div class="input-group">
    <input type="text" name="q" class="form-control" placeholder="{{ trans('app.search_your_family_placeholder') }}" value="{{ request('q') }}">
    <span class="input-group-btn">
        <button type="submit" class="btn btn-default">{{ trans('app.search') }}</button>
        <a href="{{ route('users.search') }}" class="btn btn-default">Reset</a>
    </span>
</div>
</form>

@if (request('q'))
<br>
{{ $users->appends(Request::except('page'))->render() }}
@foreach ($users->chunk(4) as $chunkedUser)
<div class="row">
    @foreach ($chunkedUser as $user)
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading text-center">
                {{ userPhoto($user, ['style' => 'width:100%;max-width:300px']) }}
                @if ($user->age)
                    {!! $user->age_string !!}
                @endif
            </div>
            <div class="panel-body">
                <h3 class="panel-title">{{ $user->profileLink() }} ({{ $user->gender }})</h3>
                <div>{{ trans('user.nickname') }} : {{ $user->nickname }}</div>
                <hr style="margin: 5px 0;">
                <div>{{ trans('user.father') }} : {{ $user->father_id ? $user->father->name : '' }}</div>
                <div>{{ trans('user.mother') }} : {{ $user->mother_id ? $user->mother->name : '' }}</div>
            </div>
            <div class="panel-footer">
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-default btn-xs">{{ trans('app.show_profile') }}</a>
                <a href="{{ route('users.chart', $user->id) }}" class="btn btn-default btn-xs">{{ trans('app.show_family_chart') }}</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endforeach

{{ $users->appends(Request::except('page'))->render() }}
@endif
@endsection