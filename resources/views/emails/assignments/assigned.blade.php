<!DOCTYPE html>
<html>

<head>
    <title>Penugasan Baru</title>
</head>

<body>
    <h1>Halo {{ $assignment->reporter->name }},</h1>
    <p>Anda telah menerima tugas liputan baru:</p>
    <ul>
        <li><strong>Judul:</strong> {{ $assignment->title }}</li>
        <li><strong>Lokasi:</strong> {{ $assignment->location_name }}</li>
        <li><strong>Deadline:</strong> {{ $assignment->deadline->translatedFormat('d F Y, H:i') }}</li>
    </ul>
    <p>Silakan login ke sistem untuk melihat detail dan mengambil tindakan.</p>
    <p><a href="{{ route('assignments.show', $assignment->id) }}">Lihat Penugasan</a></p>
</body>

</html>