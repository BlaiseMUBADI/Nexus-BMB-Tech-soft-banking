<!DOCTYPE html>
<html lang="fr">
    <head>
        @include('layouts.head')
    </head>
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
            @include('layouts.navbar')
            @include('layouts.sidebar')

            <main class="content-wrapper">
                @yield('content')
            </main>

            
            @include('layouts.footer')
        </div>
    </body>
</html>