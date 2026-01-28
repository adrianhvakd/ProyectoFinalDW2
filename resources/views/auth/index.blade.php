@extends('layouts.public.app')

@section('content')
    <livewire:auth.form :type="$type" />
@endsection
