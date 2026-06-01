<form action="{{ route('studio.store') }}" method="POST">
    @csrf

    <input type="text" name="nama_band" placeholder="Nama Band">
    <input type="date" name="tanggal">
    <input type="text" name="jam" placeholder="Jam">
    <input type="number" name="durasi" placeholder="Durasi">

    <button type="submit">Simpan</button>
</form>