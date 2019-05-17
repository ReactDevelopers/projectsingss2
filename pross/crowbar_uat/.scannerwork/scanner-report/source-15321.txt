@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="message">
            {{ ___alert((!empty($alert))?$alert:($errors->has('alert'))?$errors->first('alert'):'') }}
        </div>
        <div class="panel">
            <form method="POST" action="{{ url(sprintf('%s/_contact',ADMIN_FOLDER)) }}" class="form-horizontal login-form">
                <div class="panel-body">
                    {{ csrf_field() }}
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group has-feedback{{ $errors->has('message') ? ' has-error' : '' }}">
                            <label>{{trans('website.W0548')}}</label>
                            <div>
                                <textarea name="message" type="text" rows="6" class="form-control">{{ old('message',(!empty($message))?$message:'') }}</textarea>
                                @if ($errors->has('message'))
                                    <span class="help-block">{{ $errors->first('message') }}</span>
                                @endif
                            </div>
                        </div> 
                    </div>
                </div>                                    
                <div class="panel-footer">
                    <button type="submit" class="btn btn-default">{{trans('website.W0013')}}</button>
                </div>                                
            </form> 
        </div>                                
    </section>
@endsection