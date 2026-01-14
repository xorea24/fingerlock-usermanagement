@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                     <x-adminlte-datatable id="table1" :heads="$heads" :config="$config" bordered striped hoverable>
                        @foreach($positions as $position)
                            <tr>
                                <td>{{ $position->id }}</td>
                                <td>{{ $position->name }}</td>
                                <td>
                                    <button class="btn btn-xs btn-default text-teal mx-1 shadow" title="Details"><i class="fa fa-lg fa-fw fa-eye"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@endsection