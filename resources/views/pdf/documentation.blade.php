<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentation</title>
    <style>
        .container {
            width: 600px;
            margin: 0 auto;
        }

        .container img {
            display: block;
            margin-bottom: 50px;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        @foreach($documentations as $documentation)
            <img src="{{ asset('images/documentation/' . $documentation->image) }}" alt="{{ $documentation->name }}">
        @endforeach
    </div>
</body>
</html>