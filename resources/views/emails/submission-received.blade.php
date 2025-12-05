<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background:#f4f6fb; margin:0; padding:24px; }
        .card { max-width:600px; margin:0 auto; background:#ffffff; border-radius:14px; padding:24px; box-shadow:0 10px 30px rgba(16,24,40,0.08); }
        .pill { display:inline-block; padding:6px 12px; border-radius:999px; background:#e8ecff; color:#3440c5; font-weight:700; font-size:0.85rem; }
        .title { margin:16px 0 8px; font-size:1.3rem; font-weight:800; color:#111827; }
        .meta { color:#6b7280; margin:0; }
        .cta { margin-top:18px; display:inline-block; padding:12px 18px; border-radius:10px; background:linear-gradient(120deg,#5c6af0,#1cb58f); color:#fff; font-weight:700; text-decoration:none; }
        .footer { margin-top:18px; color:#9ca3af; font-size:0.9rem; }
        .list { margin:14px 0; padding:0; list-style:none; }
        .list li { margin:6px 0; color:#374151; }
    </style>
</head>
<body>
    <div class="card">
        <div class="title">Customer {{ $user->email }} just uploaded a file for checking</div>
        <p class="meta">Below are the details of the submission.</p>

        <ul class="list">
            <li><strong>Submission #:</strong> {{ $submissionNumber }}</li>
            <li><strong>File name:</strong> {{ $originalName }}</li>
            <li><strong>Customer:</strong> {{ $user->email }}</li>
        </ul>

    </div>
</body>
</html>
