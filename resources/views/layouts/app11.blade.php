<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tournament App</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* simple small style for bracket columns */
    .round-column { min-width:220px; }
    .match-box { border:1px solid #ddd; padding:8px; margin-bottom:8px; background:#fff; }
  </style>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="p-4">
<div class="container">
  <h2 class="mb-3">Tournament App</h2>

  @if(session('success'))
      <div class="alert alert-success alert-message">{{ session('success') }}</div>
  @endif
  @if(session('error'))
      <div class="alert alert-danger alert-message">{{ session('error') }}</div>
  @endif

  @yield('content')
</div>

<script>
  $(document).ready(function(){
      // Hide success/error alert after 3 seconds smoothly
      setTimeout(function(){
          $(".alert-message").fadeOut('slow');
      }, 3000);
  });
</script>
</body>
</html>
