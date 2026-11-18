<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enrollment closed</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f4f6fb; font-family: system-ui, -apple-system, 'Segoe UI', Arial, sans-serif; color: #14264f; padding: 20px; }
        .card { max-width: 480px; width: 100%; background: #fff; border-radius: 18px; padding: 32px 28px; text-align: center; box-shadow: 0 10px 30px rgba(20, 38, 79, .1); }
        h1 { margin: 0 0 8px; font-size: 22px; }
        .school { margin: 0 0 18px; font-size: 13px; color: #6b768c; }
        p { margin: 0 0 20px; line-height: 1.6; }
        a { display: inline-block; padding: 11px 20px; border-radius: 10px; background: #14264f; color: #fff; text-decoration: none; font-weight: 700; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Online enrollment is closed</h1>
        <p class="school">{{ $schoolName }}</p>
        <p>{{ $message }}</p>
        <a href="{{ route('enrollment.track') }}">Track my application</a>
    </div>
</body>
</html>
