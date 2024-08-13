@extends('layouts.app')

@section('main')
<div class="container">
        <div class="row my-5">
            <div class="col-md-3">
            @include('layouts.sidebar')
            </div>
            <div class="col-md-9">
            @include('layouts.message')
                <div class="card border-0 shadow">
                    <div class="card-header  text-white">
                        Profile
                    </div>
                    <div class="card-body">
                        <form action="{{route('account.updatePassword')}}" method="post" >
                            @csrf
                            <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" value="" placeholder="Password" >
                                @error('password')
                                    <p class="invalid-feedback">{{$message}}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                            <label for="password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password_confirmation" value="" placeholder="Confirm Password" >
                                @error('password_confirmation')
                                    <p class="invalid-feedback">{{$message}}</p>
                                @enderror
                            </div> 
                            <button class="btn btn-primary mt-2">Update</button>
                        </form>
                    </div>
                </div>                
            </div>
        </div>       
    </div>
@endsection