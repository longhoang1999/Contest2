@extends('layouts/default')
@section('title')
    Dashboard
@parent
@stop

@section('header_styles')
    <style>

    </style>
@stop

@section('title_page')
    Dashboard
@stop

@php
    use Cartalyst\Sentinel\Native\Facades\Sentinel;
    use Illuminate\Support\Facades\DB;
@endphp
@section('content')
    <div class="d-flex" id="main-wedsite">
        <div id="kt_app_content_container" class="app-container container-fluid">
            <div class="row">
                <div class="col-xl-12 mb-10">
                   {{-- Content --}}
                </div>
            </div>

        </div>
    </div>
@stop

@section('footer_scripts')
    <script src="{{ asset('chart.js') }}"></script>
    <script>
        var dd = document.querySelector(".dashboard")
        dd.classList.add("show")
        $(".dashboard-navbar").addClass("here")
        $(".dashboard-navbar").addClass("show")
    </script>
@stop
