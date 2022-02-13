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
                    Te informamos que quedan queda 1 dia para enviar el informe técnico, de la denuncia registrada con
                    código
                    <span style="font-weight: bold; font-size:14px">{{ $msg['cod'] }}</span>.
                </p>
                <br>

                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Estado: <span style="font-weight: bold; font-size:14px">{{ $msg['state'] }}</span>

                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Puedes ingresar a la plataforma en el siguiente link: <a href="denuncias.coralina.gov.co">
                        denuncias.coralina.gov.co
                    </a>
                </p>

            </th>

        </tr>
    </tbody>
@endsection
