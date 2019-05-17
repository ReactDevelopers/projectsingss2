<div class="box box-warning direct-chat direct-chat-warning">
    <div class="box-body">
        <div class="direct-chat-messages">
            @if(!empty($chat))
                @foreach($chat as $item => $value)
                    @if($value['sender_type'] == 'employer')
                        <div class="direct-chat-msg">
                            <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-left">Employer : {{ $value['sender'] }}</span>
                                <span class="direct-chat-timestamp pull-right">{{ ___d($value['created']) }}</span>
                            </div>
                            <img class="direct-chat-img" src="{{ $value['sender_picture'] }}" alt="message user image">
                            <div class="direct-chat-text">
                                @if($value['message_type'] == 'image')
                                    <img src="{{ $value['message'] }}" alt="Image">
                                @else
                                    {{ $value['message'] }}
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="direct-chat-msg right">
                            <div class="direct-chat-info clearfix">
                                <span class="direct-chat-name pull-right">Talent : {{ $value['sender'] }}</span>
                                <span class="direct-chat-timestamp pull-left">{{ ___d($value['created']) }}</span>
                            </div>
                            <img class="direct-chat-img" src="{{ $value['sender_picture'] }}" alt="message user image">
                            <div class="direct-chat-text">
                                @if($value['message_type'] == 'image')
                                    <img src="{{ $value['message'] }}" alt="Image">
                                @else
                                    {{ $value['message'] }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                {{ B_N_A }}
            @endif
        </div>
    </div>
</div>