<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name') }}</title>
  <!-- Fonts -->
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <!-- Styles -->
  <link rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
</head>
<body>
  <div id="app" class="wrap"></div>
  <script src="{{ mix('js/app.js') }}" defer></script>
  <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
</body>
</html>
