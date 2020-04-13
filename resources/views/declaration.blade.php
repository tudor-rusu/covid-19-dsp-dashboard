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
                    {{ __('app.Declaration header') }} @if ($declaration) <strong>{{ $declaration['code'] }}</strong>
                    @endif
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
                    <section id="declaration-view">
                        <div class="row border border-dark" id="header-declaration">
                            <div class="col-md-4 offset-4 text-center">
                                <h4 class="text-uppercase">{{ __('app.Declaration') }}</h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <h4 class="text-uppercase">{{ ( app()->getLocale()== 'ro') ? 'RO/EN' : 'EN/RO' }}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    @if ( app()->getLocale() == 'ro' )
                                    <tr>
                                        <td width="20%">{{ __('app.Name in declaration') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['name'] }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%">{{ __('app.Surname') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['surname'] }}</strong></td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td width="20%">{{ __('app.Surname') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['surname'] }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%">{{ __('app.Name in declaration') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['name'] }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td width="20%">{{ __('app.Sex') }}:</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="sex-male"
                                                       onclick="return false;"{{ $declaration['sex'] === 'M' ? 'checked' : ''
                                                }}>
                                                <label class="form-check-label" for="sex-male"><strong>M</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="sex-female"
                                                       onclick="return false;" {{ $declaration['sex'] === 'F' ? 'checked' : ''
                                                }}>
                                                <label class="form-check-label" for="sex-female"><strong>F</strong></label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">{{ __('app.Travelling from country') }}:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_country'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.City') }}:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_city'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.Date') }}:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_date'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">
                                            @if ( $declaration['document_type'] == 'passport')
                                            {{ __('app.Passport') }} /
                                            <span style="text-decoration: line-through;">{{ __('app.ID') }}:</span>
                                            @else
                                            <span style="text-decoration: line-through;">
                                                {{ __('app.Passport')}}
                                            </span> / {{ __('app.ID') }}:
                                            @endif
                                        </td>
                                        <td>
                                            {{ __('app.Series')}}:
                                            <strong class="text-uppercase">
                                                {{ $declaration['document_series'] }}
                                            </strong>
                                            {{ __('app.No')}}:
                                            <strong class="text-uppercase">
                                                {{ $declaration['document_number'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.Date of birth') }}</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['birth_date'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.Date of arrival') }}</td>
                                        <td>___________________</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </section>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
