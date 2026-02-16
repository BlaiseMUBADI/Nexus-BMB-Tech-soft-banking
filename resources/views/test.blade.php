@extends('layouts.app')

@section('page_title', 'Test route')
@section('breadcrumb_parent', 'Accueil')
@section('breadcrumb', 'Test')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-4 text-success">Test OK !</h1>
    <p class="lead">La route personnalisée fonctionne parfaitement.</p>
</div>
@endsection
