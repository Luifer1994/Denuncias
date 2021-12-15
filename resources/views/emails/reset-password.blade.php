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
                    Parece que olvidó su contraseña para Denuncias Coralina. Si esto es cierto, copia el codigo a
                    continuación para restablecer su contraseña.
                    <br>
                    Código : <span style="font-size: 15px">{{ $msg['cod'] }}</span>
                    <br>
                    Si no olvidó su contraseña, ignore este correo electrónico.
                </p>
            </th>

        </tr>
    </tbody>
@endsection
