@extends('errors::minimal')

@section('title', __('Lagi ke toilet'))
@section('code', '503')
@section('message', __($exception->getMessage() ?: 'Lagi ke toilet'))
