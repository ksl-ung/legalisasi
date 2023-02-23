<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  @if (isset($output))
    <pre>
      @foreach ($output as $o)
        {{ $o }}
      @endforeach
    </pre>
  @endif

  <form method="post">
    @csrf
    <input name="prompt" type="text">
    <button type="submit">submit</button>
  </form>
</body>

</html>
