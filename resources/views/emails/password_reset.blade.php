@component('emails.layout')
    <div>
        <div style="text-align: center;">
            <a href="{{ config('app.url') }}">
                LOGO
            </a>
        </div>

        <div style="padding: 30px;">
            <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
                <p style="font-weight: bold;font-size: 20px;color: #242424;line-height: 24px;">
                    Witaj {{ $receiver }}
                </p>

                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    Aby zresetować swoje hasło kliknij <a href="{{ $redirectUrl }}" >tutaj</a>.
                </p>
            </div>
        </div>
    </div>
@endcomponent
