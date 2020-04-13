@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('message'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('app.Declaration') }} @if ($declaration) <strong>{{ $declaration['code'] }}</strong> @endif
                    <div class="float-right">
                        <form method="POST" action="{{ route('change-lang') }}">
                            @csrf
                            <div class="form-group row" id="change-language">
                                <select id="lang" name="lang" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="ro"{{ ( app()->getLocale()== 'ro') ? ' selected' : ''
                                    }}>{{ __('app.romanian') }}</option>
                                    <option value="en"{{ ( app()->getLocale()== 'en') ? ' selected' : ''
                                    }}>{{ __('app.english') }}</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($declaration)
                        {{ $declaration['name'] }} <br>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
