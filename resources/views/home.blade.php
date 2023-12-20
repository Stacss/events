@extends('adminlte::page')

@section('content')

    <div class="row justify-content-center pt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('dashboard.dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('dashboard.you_are_logged_in') }}


                </div>
            </div>
        </div>
    </div>

@endsection
