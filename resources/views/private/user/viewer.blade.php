@extends('layouts.private.visor')

@section('title', 'Visor de Documentos')

@section('content')

    @livewire('private.pdf', ['document' => $document])

@endsection
