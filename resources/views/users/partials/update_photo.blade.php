<div class="panel panel-default">
    <div class="panel-heading"><h3 class="panel-title">{{ __('user.update_photo') }}</h3></div>
    <div class="panel-body text-center">
        {{ userPhoto($user, ['style' => 'width:100%;max-width:300px']) }}
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label for="photo">{{ __('user.reupload_photo') }}</label>
            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png">
            <small class="form-text text-warning">Format jpg, maks: 200 Kb.</small>
        </div>
    </div>
    <div class="panel-footer">
        <button type="submit" class="btn btn-success">{{ __('user.update_photo') }}</button>
        <a href="{{ route('users.show', $user) }}" class="btn btn-default">{{ __('app.cancel') }}</a>
    </div>
</div>