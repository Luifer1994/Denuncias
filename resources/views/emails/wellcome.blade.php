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
                    Te damos la bienvenida a Coralina App, La aplicación donde
                    podrás realizar denuncias ambientales
                    y cuidar juntos el medio ambiente.
                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Tus credenciales para ingresar al sistema son los siguientes.
                </p>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Usuario: {{ $msg['email'] }}
                    <br>
                    Contraseña: {{ $msg['password'] }}
                    <br>
                    @if ($msg['isofficial'])
                        Puedes ingresar a la plataforma en el siguiente link: denuncias.coralina.gov.co
                        <br>
                    @endif
                    Si necesitas ayuda, escríbenos a denuncias@coralina.gov.co
                </p>

            </th>

        </tr>
    </tbody>
@endsection
