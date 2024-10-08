<!-- View: Lista -->
<h1>Lista</h1>
<p>This is the Lista view.</p>


@foreach($items as $item)
    <h2>ID: {{ $item->name }}</h2>
@endforeach


@import->hol