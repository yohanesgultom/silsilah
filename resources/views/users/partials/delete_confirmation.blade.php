<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ __('user.delete') }} : {{ $user->name }}</h3></div>
    <div class="panel-body">
        <table class="table table-condensed">
            <tr><td>{{ __('user.name') }}</td><td>{{ $user->name }}</td></tr>
            <tr><td>{{ __('user.nickname') }}</td><td>{{ $user->nickname }}</td></tr>
            <tr><td>{{ __('user.gender') }}</td><td>{{ $user->gender }}</td></tr>
            <tr><td>{{ __('user.father') }}</td><td>{{ $user->father_id ? $user->father->name : '' }}</td></tr>
            <tr><td>{{ __('user.mother') }}</td><td>{{ $user->mother_id ? $user->mother->name : '' }}</td></tr>
            <tr><td>{{ __('user.childs_count') }}</td><td>{{ $childsCount = $user->childs()->count() }}</td></tr>
            <tr><td>{{ __('user.spouses_count') }}</td><td>{{ $spousesCount = $user->marriages()->count() }}</td></tr>
            <tr><td>{{ __('user.managed_user') }}</td><td>{{ $managedUserCount = $user->managedUsers()->count() }}</td></tr>
            <tr><td>{{ __('user.managed_couple') }}</td><td>{{ $managedCoupleCount = $user->managedCouples()->count() }}</td></tr>
        </table>
        @if ($childsCount + $spousesCount + $managedUserCount + $managedCoupleCount)
            {{ __('user.replace_delete_text') }}
            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('{{ __('user.replace_confirm') }}')">
                @csrf
                @method('DELETE')
                <div class="form-group">
                    <label for="replacement_user_id">{{ __('user.replacement') }}</label>
                    <select name="replacement_user_id" class="form-control">
                        <option value="">{{ __('user.replacement') }}</option>
                        @foreach($replacementUsers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" name="replace_delete_button" class="btn btn-danger">{{ __('user.replace_delete_button') }}</button>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-default pull-right">{{ __('app.cancel') }}</a>
            </form>
        @else
            <form method="POST" action="{{ route('users.destroy', $user) }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <button type="submit" class="btn btn-danger">{{ __('user.delete_confirm_button') }}</button>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-default">{{ __('app.cancel') }}</a>
            </form>
        @endif
    </div>
</div>