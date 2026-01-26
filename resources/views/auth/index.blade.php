@extends('layouts.app')

@section('content')
    <livewire:auth.form :type="$type" />
@endsection
