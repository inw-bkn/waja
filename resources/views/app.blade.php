<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,400;0,600;0,700;1,100;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
        <style>
            .bg-pattern {
                background-image: url('{{ asset('image/bg-pattern.svg') }}');
                background-repeat: repeat;
            }
        </style>
        <script src="{{ mix('/js/app.js') }}" defer></script>
    </head>
    <body>
        @inertia
    </body>
</html>
