@extends('layouts.master')

@section('title', 'Служба безопасности')

@section('content')
    <div class="col-sm-7 col-md-5 col-lg-5 col-xs-24">
        @include('shop.service.security.components.sidebar')
    </div>

    <div class="col-sm-17 col-md-19 col-lg-19 col-xs-24 pull-right animated fadeIn">
        <div class="well block">
            <h3>Изменение настроек интеграций</h3>

            <hr class="small" />
            <h4>Синхронизация с каталогом:</h4>
            <div class="row">
                <div class="col-md-24">
                    <form action="{{ url('/shop/service/security/integrations') }}" method="post">
                        {{ csrf_field() }}

                        @if(is_null($propertiesProvider->getBool(\App\Providers\DynamicPropertiesProvider::KEY_INTEGRATION_CATALOG)))
                            <input type="hidden" name="enabled" value="0">
                            <p><b class="text-success">Включена</b>, настройка не перезаписана.</p>

                            <div class="row margin-bottom-1">
                                <div class="col-lg-9 col-md-11 col-sm-16 col-xs-24">
                                    <button type="submit" class="btn btn-danger">Выключить</button>
                                </div>
                            </div>
                        @else
                            <p>
                                <b class="text-danger">Выключена</b>, настройка перезаписана.
                            </p>

                            <div class="row margin-bottom-1">
                                <div class="col-lg-9 col-md-11 col-sm-16 col-xs-24">
                                    <button type="submit" class="btn btn-success">Включить</button>
                                </div>
                            </div>
                            <input type="hidden" name="enabled" value="1">
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection