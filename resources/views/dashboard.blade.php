<!DOCTYPE html>
<html lang="th-TH">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Dashboard</title>
</head>
<body>
    <h1>Hello {{ Auth::user()->name }}</h1>
    <form action="{{ url('logout') }}" method="POST">
        @csrf
        <input type="submit" value="logout">
    </form>
</body>
</html>
