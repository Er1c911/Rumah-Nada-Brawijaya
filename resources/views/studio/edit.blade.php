<form action="{{ route('studio.update', $studio->id) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="nama_band" value="{{ $studio->nama_band }}">
    <input type="date" name="tanggal" value="{{ $studio->tanggal }}">
    <input type="text" name="jam" value="{{ $studio->jam }}">
    <input type="number" name="durasi" value="{{ $studio->durasi }}">

    <button type="submit">Update</button>
</form>