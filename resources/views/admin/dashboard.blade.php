<p>Bienvenido, {{ auth()->user()->name }}. Este es tu panel de administrador.</p>
<!-- Aquí puedes agregar enlaces y funcionalidades específicas para el administrador -->
@include('admin.workers.create')
