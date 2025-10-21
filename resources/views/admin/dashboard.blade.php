@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tableau de bord administrateur</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>Bienvenue sur le tableau de bord administrateur.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.challenges.index') }}" class="btn btn-primary">
                            Gérer les défis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
