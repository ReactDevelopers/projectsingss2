@extends('layouts.backend.dashboard')

@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-group">
                            <h2 >Event Title</h2>
                            <p>{{$event['event_title']}}</p>
                        </div>
                        <div class="form-group">
                            <h3>Event Description</h3>
                            <p>{{$event['event_description']}}</p>
                        </div>
                        <div class="form-group">
                            <h3>Event Date Time</h3>
                            <p>{{date('d-M-y',strtotime($event['event_date']))}}  {{$event['event_time']}}</p>
                        </div>
                        <div class="form-group">
                            <h3>Location</h3>
                            <p>{{$event['location']}} , {{$event['country_name']['country_name']}}, {{$event['state_name']['state_name']}}, {{$event['city_name']['city_name']}}</p>
                        </div>
                        <div class="form-group">
                            <h3>Entry Fee</h3>
                            <p>{{$event['entry_fee']}}</p>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="{{ $backurl }}" class="btn btn-default">Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="hire-me" >
            <div >
            </div>
        </div>
    </section>
@endsection