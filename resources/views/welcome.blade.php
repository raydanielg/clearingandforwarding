@extends('layouts.public')
@section('title', config('app.name', 'CAF') . ' — Clearing & Forwarding')

@section('content')
@include('public.partials.header')
@include('public.partials.hero')
@include('public.partials.services')
@include('public.partials.about')
@include('public.partials.features')
@include('public.partials.tracking')
@include('public.partials.stats')
@include('public.partials.contact')
@include('public.partials.footer')
@endsection
