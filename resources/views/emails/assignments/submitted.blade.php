<!DOCTYPE html>
<html>

<head>
    <title>Laporan Liputan Baru</title>
</head>

<body>
    <h1>Halo Editor,</h1>
    <p>Wartawan <strong>{{ $assignment->reporter->name }}</strong> telah mengirimkan laporan untuk penugasan:</p>
    <ul>
        <li><strong>Judul:</strong> {{ $assignment->title }}</li>
        <li><strong>Status:</strong> Submitted</li>
    </ul>
    <p>Silakan periksa laporan tersebut di Review Console.</p>
    <p><a href="{{ route('reviews.show', $assignment->id) }}">Review Laporan</a></p>
</body>

</html>