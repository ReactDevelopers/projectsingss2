<div class="postjob-beforesubmit premiumAccount">
    <div class="col-md-12">
        <div class="premiumAccountSec">
            <div id="message"></div>
            <form class="form-horizontal" role="add_card" action="{{url(sprintf('%s/payment/card/add?save_card=on&redirect=%s',EMPLOYER_ROLE_TYPE,urlencode(\Request::get('redirect'))))}}" method="post" accept-charset="utf-8">
                <div class="card-box">
                    @php
                        foreach ($cards as $key => $item){
                            $url_delete = sprintf(
                                url('%s/payment/card/delete?card_id=%s'),
                                EMPLOYER_ROLE_TYPE,
                                $item['id_card']
                            );
                            
                            echo sprintf(
                                ADD_CARD_TEMPLATE,
                                $item['id_card'],
                                $item['id_card'],
                                ($item['default'] == DEFAULT_YES_VALUE)?'checked="checked"':'',
                                $item['image_url'],
                                sprintf("%s %s",wordwrap(str_repeat(".",strlen($item['masked_number'])-4),4,' ',true),$item['last4']),
                                ($item['default'] == DEFAULT_YES_VALUE)?trans('website.W0427'):'',
                                $url_delete,
                                $item['id_card'],
                                asset('/'),
                                asset('/')
                            );
                        }
                    @endphp
                </div>
                <div class="fill-card-details">
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0737')}}</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="custom-dropdown">
                                        <select name="credit_card[card_type]" class="form-control">
                                            {!!
                                                ___dropdown_options(
                                                    \Cache::get('card_type'),
                                                    trans('website.W0737')
                                                ) 
                                            !!}
                                        </select>
                                    </div>
                                    <input type="hidden" name="card_type" />
                                </div>
                            </div>                                                                            
                            <div class="form-group">
                                <label class="control-label col-md-12 col-sm-12 col-xs-12">Card Holder Name</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input type="text" name="credit_card[cardholder_name]" placeholder="Enter card holder's name" class="form-control" />
                                    <input type="hidden" name="cardholder_name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0744')}}</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input type="text" id="credit_card_number" name="credit_card[number]" placeholder="{{trans('website.W0745')}}" class="form-control" maxlength="{{CARD_LENGTH}}" data-request="numeric"/>
                                    <input type="hidden" name="number" />
                                </div>
                            </div>
                            @if(0)
                                <div class="form-group">
                                    <label class="control-label col-md-12 col-sm-12 col-xs-12">Default</label>                            
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <div class="radio radio-inline"> 
                                            <input type="radio" name="default" id="default-yes" value="yes" checked="checked">
                                            <label for="default-yes"><span class="check"></span> Yes</label>
                                        </div>
                                        <div class="radio radio-inline"> 
                                            <input type="radio" name="default" id="default-no" value="no">
                                            <label for="default-no"><span class="check"></span> No</label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0738')}}</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="custom-dropdown">
                                        <select name="credit_card[expiry_month]" placeholder="{{sprintf(trans('website.W0739'),trans('website.W0738'))}}" class="form-control">
                                            {!!
                                                ___dropdown_options(
                                                    trans('website.W0048'),
                                                    trans('website.W0738')
                                                ) 
                                            !!}
                                        </select>
                                    </div>
                                    <input type="hidden" name="expiry_month" />
                                </div>
                            </div>                                        
                            <div class="form-group">
                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0740')}}</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="custom-dropdown">
                                        <select name="credit_card[expiry_year]" placeholder="{{trans('website.W0741')}}" class="form-control">
                                            {!!
                                                ___dropdown_options(
                                                    ___range(
                                                        range(
                                                            (int)date('Y')+CREDIT_CARD_MIN_YEAR_LIMIT,
                                                            (int)date('Y')+CREDIT_CARD_MAX_YEAR_LIMIT
                                                        )
                                                    ),
                                                    'Expiry Year'
                                                )
                                            !!}
                                        </select>
                                    </div>
                                    <input type="hidden" name="expiry_year" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-12 col-sm-12 col-xs-12">{{trans('website.W0742')}}</label>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <input type="text" name="credit_card[cvv]" placeholder="{{trans('website.W0743')}}" class="form-control" maxlength="{{CARD_CVV_LENGTH}}" data-request="numeric"/>
                                    <input type="hidden" name="cvv" />
                                </div>
                            </div>                                        
                        </div>
                    </div>
                </div>
                <div class="form-group button-group">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row form-btn-set">
                            <div class="col-md-7 col-sm-7 col-xs-6">
                                <a href="{{url(sprintf('%s/payments/settings',EMPLOYER_ROLE_TYPE))}}" class="greybutton-line" value="{{trans('website.W0355')}}">{{trans('website.W0355')}}</a>
                            </div>
                            <div class="col-md-5 col-sm-5 col-xs-6">
                                <button type="button" type="button" data-box=".card-box" data-request="multi-ajax" data-message="#message" data-target='[role="add_card"]' data-box-id="[name='id_card']" data-toremove="box" class="button" value="{{trans('website.W0611')}}">{{trans('website.W0611')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('inlinescript')
    <script>
        function credit_card_number_format(value) {
            var v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '')
            var matches = v.match(/\d{4,16}/g);
            var match = matches && matches[0] || ''
            var parts = []
            for (i=0, len=match.length; i<len; i+=4) {
                parts.push(match.substring(i, i+4))
            }
            
            if (parts.length) {
                return parts.join(' ')
            } else {
                return value
            }
        }

        onload = function() {
            document.getElementById('credit_card_number').oninput = function() {
                this.value = credit_card_number_format(this.value)
            }
        }
    </script>
@endpush