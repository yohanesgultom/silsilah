<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="yod">{{ __('user.yod') }}</label>
            <input type="text" name="yod" class="form-control" placeholder="{{ __('app.example').' 2003' }}" value="{{ old('yod', $user->getMetadata('yod')) }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="dod">{{ __('user.dod') }}</label>
            <input type="text" name="dod" class="form-control" placeholder="{{ __('app.example').' 2003-10-17' }}" value="{{ old('dod', $user->getMetadata('dod')) }}">
        </div>
    </div>
</div>

<fieldset>
    <legend>{{ __('user.cemetery_location') }}</legend>
    <div class="form-group">
        <label for="cemetery_location_name">{{ __('address.location_name') }}</label>
        <input type="text" name="cemetery_location_name" class="form-control" value="{{ old('cemetery_location_name', $user->getMetadata('cemetery_location_name')) }}">
    </div>
    <div class="form-group">
        <label for="cemetery_location_address">{{ __('address.address') }}</label>
        <textarea name="cemetery_location_address" class="form-control">{{ old('cemetery_location_address', $user->getMetadata('cemetery_location_address')) }}</textarea>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="cemetery_location_latitude">{{ __('address.latitude') }}</label>
                <input type="text" name="cemetery_location_latitude" class="form-control" value="{{ old('cemetery_location_latitude', $user->getMetadata('cemetery_location_latitude')) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="cemetery_location_longitude">{{ __('address.longitude') }}</label>
                <input type="text" name="cemetery_location_longitude" class="form-control" value="{{ old('cemetery_location_longitude', $user->getMetadata('cemetery_location_longitude')) }}">
            </div>
        </div>
    </div>
</fieldset>