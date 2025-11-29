@extends('layouts.app')

@section('content')
<div class="row" style="height: calc(100vh - 100px);">
    
    <!-- Левая часть -->
    <div class="col-8 border-end" style="overflow-y: auto;">
        @yield('client-list')
    </div>

    <!-- Правая часть -->
    <div class="col-4" id="client-preview" style="overflow-y: auto;">
        <div class="text-center mt-5 text-muted">
            <p>Выберите клиента слева</p>
        </div>
    </div>

</div>

<script>
function loadClientPreview(id) {
    fetch(`/clients/${id}/preview`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('client-preview').innerHTML = html;
        });
}
</script>
@endsection
