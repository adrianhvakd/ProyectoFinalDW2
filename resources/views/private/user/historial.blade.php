@extends('layouts.private.sidebar')
@section('title', 'Historial')

@section('content')
    @livewire('private.historial', ['id' => auth()->user()->id])
@endsection
