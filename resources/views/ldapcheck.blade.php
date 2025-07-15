<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LDAP Check</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label, input {
            display: block;
            width: 300px;
        }
        input {
            padding: 8px;
            margin-top: 5px;
        }
        button {
            padding: 8px 16px;
        }
        .message {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>LDAP User Credential Check</h2>

    @if(session('message'))
        <div class="message">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ url('/ldap-check') }}">
        @csrf
        <div class="form-group">
            <label for="username">Username (samaccountname):</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Check</button>
    </form>

</body>
</html>
