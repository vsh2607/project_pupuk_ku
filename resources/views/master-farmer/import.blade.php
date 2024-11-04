<form method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="import_file">
    <button type="submit">Simpan</button>
</form>
