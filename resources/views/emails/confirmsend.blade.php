@extends('emails.layout')
@section('content')
    <tbody>
        {{-- <tr>
            <th style="padding-left: 10px;">
                <h1>Bienvenid@</h1>
            </th>

        </tr> --}}
        <tr>
            <th style="text-align: left; padding-left: 10px;">
                <h2 style="margin-top: 2px;margin-bottom: 2px;">Hola, {{ $msg['name'] }}</h2>
            </th>

        </tr>
        <tr>
            <th style="text-align: left; padding-left: 10px;">
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Tu denuncia ha sido enviada con éxito.
                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Puedes realizar seguimiento desde la App con el código <span style="font-weight: bold; font-size:14px">{{ $msg['cod'] }}</span>
                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Gracias por ayudarnos a proteger y cuidar el medio ambiente.

                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Si necesitas ayuda, Escríbenos a denuncias@coralina.gov.co
                </p>

            </th>

        </tr>
    </tbody>
@endsection
