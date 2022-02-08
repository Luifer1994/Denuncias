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
                    Estamos gestionando tu denuncia registrada con código
                    <span style="font-weight: bold; font-size:14px">{{ $msg['cod'] }}</span>,
                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Puedes realizar seguimiento desde la App con el código
                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Estado: <span style="font-weight: bold; font-size:14px">{{ $msg['state'] }}</span>

                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Recuerda que puedes hacer seguimiento a todas tus denuncias directamente desde la app en la
                    sección de buscar y dar seguimiento a denuncias.
                </p>

            </th>

        </tr>
    </tbody>
@endsection
