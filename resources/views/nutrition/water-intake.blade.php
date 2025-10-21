@extends('layouts.app')

@section('content')
<div class="container mt-5 pt-5"> {{-- Ajout de pt-5 pour plus d'espace en haut --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Water Intake Tracker</h4>
                    <p class="mb-0">{{ now()->format('l, F j, Y') }}</p>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <div class="water-bottle position-relative d-inline-block mb-3">
                            <div class="progress" style="height: 300px; width: 150px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: 100%; height: {{ $progress }}%" 
                                     aria-valuenow="{{ $progress }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <h2 class="mb-0">{{ $waterIntake->amount }}<small class="text-muted">ml</small></h2>
                                <small class="text-muted">of {{ $dailyGoal }}ml</small>
                            </div>
                        </div>
                        
                        <div class="progress mb-4" style="height: 30px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $progress }}%" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($progress, 1) }}%
                            </div>
                        </div>
                    </div>

                    <h5 class="text-center mb-4">Add Water Intake</h5>
                    
                    <div class="row justify-content-center">
                        <div class="col-md-8">
                            <form action="{{ route('nutrition.water-intake.log') }}" method="POST">
                                @csrf
                                <div class="input-group mb-3">
                                    <input type="number" 
                                           name="amount" 
                                           class="form-control form-control-lg text-center" 
                                           placeholder="Amount in ml" 
                                           min="1" 
                                           max="1000" 
                                           required>
                                    <span class="input-group-text">ml</span>
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="quick-add-buttons text-center mt-4">
                        <h6 class="mb-3">Quick Add:</h6>
                        <div class="btn-group" role="group">
                            @foreach([100, 200, 300, 500] as $amount)
                                <form action="{{ route('nutrition.water-intake.log') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="amount" value="{{ $amount }}">
                                    <button type="submit" class="btn btn-outline-info mx-1">+{{ $amount }}ml</button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .water-bottle {
        width: 150px;
        height: 300px;
        border: 3px solid #0dcaf0;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
    }
    
    .water-bottle .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        border-radius: 0;
    }
    
    .water-bottle .progress-bar {
        transition: height 0.5s ease-in-out;
    }
    
    .quick-add-buttons .btn {
        min-width: 80px;
    }
</style>
@endsection
