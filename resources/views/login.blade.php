<!DOCTYPE html>
<html lang="th-TH">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>WAJA - Login</title>
</head>
<body>
    <h1>WAJA - LOGIN</h1>
    <form action="{{ url('login') }}" method="POST">
        @csrf
        <div><label for="">email : </label><input name="email" type="text"></div>
        @error('email')
        <small style="color: red;">{{ $message }}</small>
        @enderror
        <div><label for="">password : </label><input name="password" type="password"></div>
        @error('password')
        <small style="color: red;">{{ $message }}</small> <br>
        @enderror
        <input type="submit" value="login">
    </form>
    @auth
        <h2>Hello USER</h2>
    @else
        <h2>Hello Guest</h2>
    @endif
</body>
</html>
