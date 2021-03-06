@extends('layout')
@section('content')
<h2>Up-to-date</h2>
@include('partials.updates')
<p>&nbsp;</p>
<h2>Application settings</h2>
{{ Form::open(array('route' => 'settings.store', 'action' => 'POST', 'role' => 'form')) }}
@foreach($settings as $setting)
<div class="form-group">
    <label for="{{ $setting->name }}">{{{ $setting->friendlyname }}}</label>
    @if($setting->type == 'textbox')
    <input type="text" class="form-control" name="{{ $setting->name }}" id="{{ $setting->name }}" placeholder="eg. www.mydomain.com or *.mydomain.com"@if($setting->svalue) value="{{{ $setting->svalue }}}"@endif>
    @endif
    @if($setting->type == 'dropdown')
    <select name="{{ $setting->name }}"  class="form-control">
    @foreach(explode('|', $setting->options) as $option)
    @if($option == $setting->svalue)
        <option value="{{ $option }}" selected>{{ $option }}</option>
    @else
        <option value="{{ $option }}">{{ $option }}</option>
    @endif
    @endforeach
    </select>
    @endif
    <p class="help-block">{{{ $setting->description }}}</p>
</div>
@endforeach
<!-- End of options -->
{{ Form::submit('Save changes', array('class' => 'btn btn-default')) }}
{{ Form::close() }}
</form>
<p>&nbsp;</p>

<a name="password"></a>
<h2>Admin password</h2>
<p>You are recommended that from time to time you reset your admin account '<em>{{ Auth::user()->username }}</em>' password, this password enables you to login to Turbine as well as access the API remotely.</p>
{{ Form::open(array('url' => 'password/reset', 'action' => 'POST', 'role' => 'form')) }}
<div class="form-group">
    <label for="current_password">Current password</label>
    <input type="password" class="form-control" name="current_password" id="current_password">
    <p class="help-block">To reset your password you must first provide your existing 'admin' password.</p>
</div>
<div class="form-group">
    <label for="new_password">New password</label>
    <input type="password" class="form-control" name="new_password" id="new_password">
    <p class="help-block">Enter your new password that you'd like to use and confirm it below if you wish you make changes.</p>
</div>
<div class="form-group">
    <label for="new_password_conf">Confirm new password</label>
    <input type="password" class="form-control" name="new_password_conf" id="new_password_conf">
</div>
{{ Form::submit('Update password', array('class' => 'btn btn-default')) }}
{{ Form::close() }}
<p>&nbsp;</p>
@stop