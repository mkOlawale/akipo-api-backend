<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/css/all.css">
        <title>AkipoGlobal</title>
        <script>

            (function () {
                window.Laravel = {
                    csrfToken: '{{ csrf_token() }}'
                };
            })();

</script>
    </head>
    <body>
        <div id="app">
            @if(Auth::check())
                <mainapp :user="{{Auth::User()}}"></mainapp>
            @else
                <mainapp :user="false"></mainapp>
            @endif
        </div>
    </body>
    <Script src="{{mix('/js/app.js')}}"></Script>
</html>
