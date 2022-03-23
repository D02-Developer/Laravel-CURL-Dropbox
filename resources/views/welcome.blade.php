<form action="image" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <input type="file" name="image" class="form-control">
        </div><br>
        <div class="col-md-6">
            <button type="submit" class="btn btn-success">Upload a File</button>
        </div>
    </div>
</form>