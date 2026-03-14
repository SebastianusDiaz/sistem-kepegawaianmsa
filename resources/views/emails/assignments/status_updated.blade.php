<!DOCTYPE html>
<html>

<head>
    <title>Update Status Penugasan</title>
</head>

<body>
    <h1>Halo {{ $assignment->reporter->name }},</h1>
    <p>Status penugasan Anda <strong>{{ $assignment->title }}</strong> telah diperbarui menjadi:
        <strong>{{ strtoupper($assignment->status) }}</strong>.</p>

    @if(isset($statusMessage))
        <p><strong>Catatan:</strong></p>
        <p>{{ $statusMessage }}</p>
    @endif

    <p><a href="{{ route('assignments.show', $assignment->id) }}">Lihat Detail</a></p>
</body>

</html>