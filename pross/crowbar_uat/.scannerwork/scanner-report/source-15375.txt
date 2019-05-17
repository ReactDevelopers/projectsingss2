<ul class="post-project-link">
    <li class="language-selector @if(\Cache::get('configuration')['is_language_enabled'] === 'Y') currency-selector @endif">
        <form method="get" action="{{ url('/currency') }}">
            <select name="currency" onchange="submit()" class="form-control">
                @foreach(currencies() as $currency => $sign)
                    <option value="{{ $currency }}" @if(\Session::get('site_currency') == $currency) selected="selected" @endif>{{ $currency }}</option>
                @endforeach
            </select>
        </form>
    </li>
    @if(\Cache::get('configuration')['is_language_enabled'] === 'Y')
        <li class="language-selector">
            <form method="get" action="{{ url('/language') }}">
                <select name="language" onchange="submit()" class="form-control">
                    @foreach(language() as $code => $language)
                        <option value="{{ $code }}" @if(\App::getLocale() == $code) selected="selected" @endif>{{ strtoupper($code) }}</option>
                    @endforeach
                </select>
            </form>
        </li>
    @endif
    @if(!empty($project_link))
        <li class="post-project-link"><a href="{{ url('/signup/employer') }}" class="navyblueBtn">Post a project</a></li>
    @endif
</ul>