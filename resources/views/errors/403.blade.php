@extends('errors::minimal')

@section('title', __('Ngapain kesini bang?'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Ngapain kesini bang?'))
