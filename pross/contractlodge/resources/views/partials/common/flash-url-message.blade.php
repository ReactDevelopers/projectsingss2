@if (request()->query('message') && request()->query('level'))
    <div class="alert
                alert-{{ request()->query('level') }}
                {{ request()->query('important') ? 'alert-important' : '' }}"
                role="alert"
    >
        @if (request()->query('important'))
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true"
            >&times;</button>
        @endif

        {!! request()->query('message') !!}
    </div>
@endif
