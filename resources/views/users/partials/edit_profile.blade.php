<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ __('user.edit') }}</h3></div>
    <div class="panel-body">
        <div class="form-group">
            <label for="name">{{ __('user.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
        </div>
        <div class="form-group">
            <label for="nickname">{{ __('user.nickname') }}</label>
            <input type="text" name="nickname" class="form-control" value="{{ old('nickname', $user->nickname) }}">
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="gender_id">{{ __('user.gender') }}</label>
                    <select name="gender_id" class="form-control">
                        <option value="1" @if(old('gender_id', $user->gender_id) == 1) selected @endif>{{ __('app.male_code') }}</option>
                        <option value="2" @if(old('gender_id', $user->gender_id) == 2) selected @endif>{{ __('app.female_code') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="birth_order">{{ __('user.birth_order') }}</label>
                    <input type="number" name="birth_order" class="form-control" min="1" value="{{ old('birth_order', $user->birth_order) }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="yob">{{ __('user.yob') }}</label>
                    <input type="text" name="yob" class="form-control" placeholder="{{ __('app.example').' 1959' }}" value="{{ old('yob', $user->yob) }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dob">{{ __('user.dob') }}</label>
                    <input type="text" name="dob" class="form-control" placeholder="{{ __('app.example').' 1959-07-20' }}" value="{{ old('dob', $user->dob) }}">
                </div>
            </div>
        </div>
    </div>
</div>