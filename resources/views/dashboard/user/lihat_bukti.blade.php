<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lihat Bukti/Berkas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="card mt-4">
            <div class="card-header text-center">
                Bukti/Berkas
            </div>
            <div class="card-body">
                <div class="text-center">
                    @if ($format == 'png')
                        <form action="{{ route('legalisasi/download') }}" method="POST">
                            @csrf
                            @method('get')
                            <input type="text" value="{{ $url }}" name="urlbukti" hidden>
                            <button type="submit" class="btn btn-primary btn-sm mb-3">Download
                                Gambar</button>
                        </form>
                        <img src="{{ asset('storage/' . $url) }}" class="card-img-top" alt="...">
                    @endif
                </div>

            </div>
        </div>
    </div>

</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

</html>
