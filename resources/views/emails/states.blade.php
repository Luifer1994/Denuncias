@extends('emails.layout')
@section('content')
    <tbody>
        <tr>
            <th style="text-align: left; padding-left: 10px;">
                <h2 style="margin-top: 2px;margin-bottom: 2px;">Hola, {{ $msg->userAsigne->name }}</h2>
            </th>

        </tr>
        <tr>
            <th style="text-align: left; padding-left: 10px;">
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Se te ha asignado la denuncia con código <span
                        style="font-weight: bold; font-size:14px">{{ $msg->cod }}</span>, para llevar a cabo
                    todo el proceso correspondiente con la misma.
                </p>
                <br>
                <br>
                <p style="margin-top: 2px;margin-bottom: 2px;">
                    Recuerda que puedes hacer seguimiento a todas las denuncias que se te han asignado en nuestro sistema de
                    gestión de denuncias.
                </p>

            </th>

        </tr>
    </tbody>
@endsection

