<!DOCTYPE html>
<html>
<head>
    <title>Login Admin</title>
</head>
<body>
    

<h1>Login Admin Studio Musik</h1>

<form method="POST" action="/login">

    @csrf

    <label>Username</label>
    <br>

    <input type="text" name="email">

    <br><br>

    <label>Password</label>
    <br>

    <input type="password" name="password">

    <br><br>

    <button type="submit">
        Login
    </button>

</form>

@if($errors->any())

    <p>
        {{ $errors->first() }}
    </p>

@endif

</body>
</html>