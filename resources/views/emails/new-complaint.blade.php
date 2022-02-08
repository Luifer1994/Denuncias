@extends('emails.layout')
@section('content')
    <tbody>
        <tr>
            <th style="text-align: left; padding-left: 10px;">
                <h2 style="margin-top: 2px;margin-bottom: 2px;">Hola, {{ $msg['name'] }}</h2>
            </th>

        </tr>
        <tr>
            <th style="text-align: left; padding-left: 10px;">
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Ha llegado una nueva denuncia al sistemas de Denuncias Coralina.
                    <br>
                    Código de la Denuncia: <span style="font-size: 14px">{{ $msg['cod'] }}</span>
                </p>
            </th>

        </tr>
    </tbody>
@endsection
