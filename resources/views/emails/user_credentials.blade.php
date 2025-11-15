<!DOCTYPE html>
<html>
<body>

<h2>Welcome, {{ $user->name }} 👋</h2>

<p>Your account has been created successfully.</p>

<p><strong>Email:</strong> {{ $user->email }}</p>
<p><strong>Password:</strong> {{ $password }}</p>

<br>

<a href=""
   style="padding:10px 20px;background:#0d6efd;color:#fff;text-decoration:none;border-radius:5px;">
    Login Now
</a>

<br><br>

<p>If you did not request this, please ignore this email.</p>

</body>
</html>
