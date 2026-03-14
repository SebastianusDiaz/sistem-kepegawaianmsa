<!DOCTYPE html>
<html>

<head>
    <title>Penugasan Baru Tersedia</title>
</head>

<body>
    <h1>Halo Rekan Wartawan,</h1>
    <p>Tersedia penugasan liputan baru yang dapat diambil:</p>
    <ul>
        <li><strong>Judul:</strong> {{ $assignment->title }}</li>
        <li><strong>Lokasi:</strong> {{ $assignment->location_name }}</li>
        <li><strong>Deadline:</strong> {{ $assignment->deadline->translatedFormat('d F Y, H:i') }}</li>
    </ul>
    <p>Siapa cepat dia dapat. Silakan login dan ambil penugasan ini jika Anda bersedia.</p>
    <p><a href="{{ route('assignments.show', $assignment->id) }}">Lihat Penugasan</a></p>
</body>

</html>