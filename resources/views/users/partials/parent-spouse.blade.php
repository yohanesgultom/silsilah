<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ __('user.family') }}</h3></div>

    <table class="table">
        <tbody>
            <tr>
                <th class="col-sm-4">{{ __('user.father') }}</th>
                <td class="col-sm-8">
                    @can ('edit', $user)
                        @if (request('action') == 'set_father')
                        <form method="POST" action="{{ route('family-actions.set-father', $user->id) }}" autocomplete="off">
                            @csrf
                            <select name="set_father_id" class="form-control input-sm" value="{{ $user->father_id }}">
                            <option value="">{{ __('app.select_from_existing_males') }}</option>
                            @foreach($malePersonList as $id => $name)
                                <option value="{{ $id }}" @if($id == $user->father_id) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                            <div class="input-group">
                                <input type="text" name="set_father" class="form-control input-sm" placeholder="{{ __('app.enter_new_name') }}" value="{{ old('set_father') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info btn-sm" id="set_father_button">{{ __('app.update') }}</button>
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-default btn-sm">{{ __('app.cancel') }}</a>
                                </span>
                            </div>
                        </form>
                        @else
                            {!! $user->fatherLink() !!}
                            <div class="pull-right">
                                <a href="{{ route('users.show', [$user->id, 'action' => 'set_father']) }}" class="btn btn-link btn-xs">{{ __('user.set_father') }}</a>
                            </div>
                        @endif
                    @else
                        {!! $user->fatherLink() !!}
                    @endcan
                </td>
            </tr>
            <tr>
                <th>{{ __('user.mother') }}</th>
                <td>
                    @can ('edit', $user)
                        @if (request('action') == 'set_mother')
                        <form method="POST" action="{{ route('family-actions.set-mother', $user->id) }}" autocomplete="off">
                            @csrf
                            <select name="set_mother_id" class="form-control input-sm" value="{{ $user->mother_id }}">
                            <option value="">{{ __('app.select_from_existing_females') }}</option>
                            @foreach($femalePersonList as $id => $name)
                                <option value="{{ $id }}" @if($id == $user->mother_id) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                            <div class="input-group">
                                <input type="text" name="set_mother" class="form-control input-sm" placeholder="{{ __('app.enter_new_name') }}" value="{{ old('set_mother') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-info btn-sm" id="set_mother_button">{{ __('app.update') }}</button>
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-default btn-sm">{{ __('app.cancel') }}</a>
                                </span>
                            </div>
                        </form>
                        @else
                            {!! $user->motherLink() !!}
                            <div class="pull-right">
                                <a href="{{ route('users.show', [$user->id, 'action' => 'set_mother']) }}" class="btn btn-link btn-xs">{{ __('user.set_mother') }}</a>
                            </div>
                        @endif
                    @else
                        {!! $user->motherLink() !!}
                    @endcan
                </td>
            </tr>
            <tr>
                <th class="col-sm-4">{{ __('user.parent') }}</th>
                <td class="col-sm-8">
                    @can ('edit', $user)
                    <div class="pull-right">
                        @unless (request('action') == 'set_parent')
                            <a href="{{ route('users.show', [$user->id, 'action' => 'set_parent']) }}" class="btn btn-link btn-xs">{{ __('user.set_parent') }}</a>
                        @endunless
                    </div>
                    @endcan

                    @if ($user->parent)
                    {{ $user->parent->husband->name }} & {{ $user->parent->wife->name }}
                    @endif

                    @can('edit', $user)
                        @if (request('action') == 'set_parent')
                        <form method="POST" action="{{ route('family-actions.set-parent', $user->id) }}" autocomplete="off">
                            @csrf
                            <select name="set_parent_id" class="form-control">
                                <option value="">{{ __('app.select_from_existing_couples') }}</option>
                                @foreach($allMariageList as $id => $name)
                                    <option value="{{ $id }}" @if($id == $user->parent_id) selected @endif>{{ $name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-info btn-sm" id="set_parent_button">{{ __('app.update') }}</button>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-default btn-sm">{{ __('app.cancel') }}</a>
                        </form>
                        @endif
                    @endcan
                </td>
            </tr>
            @if ($user->gender_id == 1)
            <tr>
                <th>{{ __('user.wife') }}</th>
                <td>
                    @can ('edit', $user)
                    <div class="pull-right">
                        @unless (request('action') == 'add_spouse')
                            <a href="{{ route('users.show', [$user->id, 'action' => 'add_spouse']) }}" class="btn btn-link btn-xs">{{ __('user.add_wife') }}</a>
                        @endunless
                    </div>
                    @endcan

                    @if ($user->wifes->isEmpty() == false)
                        <ul class="list-unstyled">
                            @foreach($user->wifes as $wife)
                            <li>{!! $wife->profileLink() !!}</li>
                            @endforeach
                        </ul>
                    @endif
                    @can('edit', $user)
                        @if (request('action') == 'add_spouse')
                        <form method="POST" action="{{ route('family-actions.add-wife', $user->id) }}" autocomplete="off">
                            @csrf
                            <select name="set_wife_id" class="form-control">
                                <option value="">{{ __('app.select_from_existing_females') }}</option>
                                @foreach($femalePersonList as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-7">
                                        <input type="text" name="set_wife" class="form-control input-sm" placeholder="{{ __('app.enter_new_name') }}" value="{{ old('set_wife') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="marriage_date" class="form-control input-sm" placeholder="{{ __('couple.marriage_date') }}" value="{{ old('marriage_date') }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="set_wife_button" class="btn btn-info btn-sm" id="set_wife_button">{{ __('app.update') }}</button>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-default btn-sm">{{ __('app.cancel') }}</a>
                        </form>
                        @endif
                    @endcan
                </td>
            </tr>
            @else
            <tr>
                <th>{{ __('user.husband') }}</th>
                <td>
                    @can ('edit', $user)
                    <div class="pull-right">
                        @unless (request('action') == 'add_spouse')
                            <a href="{{ route('users.show', [$user->id, 'action' => 'add_spouse']) }}" class="btn btn-link btn-xs">{{ __('user.add_husband') }}</a>
                        @endunless
                    </div>
                    @endcan
                    @if ($user->husbands->isEmpty() == false)
                        <ul class="list-unstyled">
                            @foreach($user->husbands as $husband)
                            <li>{!! $husband->profileLink() !!}</li>
                            @endforeach
                        </ul>
                    @endif
                    @can('edit', $user)
                        @if (request('action') == 'add_spouse')
                        <form method="POST" action="{{ route('family-actions.add-husband', $user->id) }}" autocomplete="off">
                            @csrf
                            <select name="set_husband_id" class="form-control">
                                <option value="">{{ __('app.select_from_existing_males') }}</option>
                                @foreach($malePersonList as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-7">
                                        <input type="text" name="set_husband" class="form-control input-sm" placeholder="{{ __('app.enter_new_name') }}" value="{{ old('set_husband') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="marriage_date" class="form-control input-sm" placeholder="{{ __('couple.marriage_date') }}" value="{{ old('marriage_date') }}">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="set_husband_button" class="btn btn-info btn-sm" id="set_husband_button">{{ __('app.update') }}</button>
                            <a href="{{ route('users.show', $user) }}" class="btn btn-default btn-sm">{{ __('app.cancel') }}</a>
                        </form>
                        @endif
                    @endcan
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>