<!DOCTYPE html>
<html lang="th-TH">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <label for="">login:</label>
        <input type="text" name="login" value="{{ $login }}" readonly> <br/>
        <label for="">display name:</label>
        <input type="text" name="name" required> <br/>
        <label for="">full name:</label>
        <input type="text" name="full_name" required> <br/>
        <label for="">full name (Eng):</label>
        <input type="text" name="full_name_en" required> <br/>
        <input type="submit" value="Create">
    </form>
</body>
</html>
