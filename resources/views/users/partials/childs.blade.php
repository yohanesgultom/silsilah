<div class="panel panel-default">
    <div class="panel-heading">
        @can ('edit', $user)
        <div class="pull-right" style="margin: -3px -6px">
            <a href="{{ route('users.show', [$user->id, 'action' => 'add_child']) }}" class="btn btn-success btn-xs">{{ __('user.add_child') }}</a>
        </div>
        @endcan
        <h3 class="panel-title">{{ __('user.childs') }} ({{ $user->childs->count() }})</h3>
    </div>

    <ul class="list-group">
        @forelse($user->childs as $child)
            <li class="list-group-item">
                {!! $child->profileLink() !!} ({{ $child->gender }}
            </li>
        @empty
            <li class="list-group-item">{{ __('app.childs_were_not_recorded') }}</li>
        @endforelse
        @can('edit', $user)
        @if (request('action') == 'add_child')
        <li class="list-group-item">
            <form method="POST" action="{{ route('family-actions.add-child', $user->id) }}" autocomplete="off">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="add_child_name">{{ __('user.child_name') }}</label>
                            <input type="text" name="add_child_name" class="form-control" placeholder="{{ __('app.example').' John Doe' }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <fieldset>
                                <label>{{ __('user.child_gender') }}</label>
                                <div>
                                    <label class="radio-inline">
                                        <input type="radio" name="add_child_gender_id" value="1"> {{ __('app.male') }}
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="add_child_gender_id" value="2"> {{ __('app.female') }}
                                    </label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="add_child_parent_id">{{ __('user.add_child_from_existing_couples', ['name' => $user->name]) }}</label>
                            <select name="add_child_parent_id" class="form-control">
                                <option value="">{{ __('app.unknown') }}</option>
                                @foreach($usersMariageList as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="add_child_birth_order">{{ __('user.birth_order') }}</label>
                            <input type="number" name="add_child_birth_order" class="form-control" min="1">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-sm">{{ __('user.add_child') }}</button>
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-default btn-sm">{{ __('app.cancel') }}</a>
            </form>
        </li>
        @endif
        @endcan
    </ul>
</div>