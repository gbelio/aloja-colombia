@component('mail::message')


<div style="text-align: center;color: #3d4852;font-size: 18px;font-weight: bold;margin-top: 0;">
    {{ $titulo }}
</div>
<br>


{!! $cuerpo !!}

<div style="text-align: center;">
    <div>
        <a href="https://www.facebook.com/alojaColombiaCom/" target="_blank"><img style="width: 35px;padding-bottom: 10px;padding-right: 10px;" src="https://alojacolombia.com/img/facebook.png"></a>
        <a href="https://www.instagram.com/aloja.colombia/" target="_blank"><img style="width: 35px;padding-bottom: 10px;padding-right: 10px;" src="https://alojacolombia.com/img/instagram.png"></a>
        <a href="https://api.whatsapp.com/send?phone=+573227704646&text=Bienvenidos%20a%20Aloja%20Colombia,%20env%C3%ADanos%20tu%20consulta%20y%20en%20breve%20nos%20contactaremos%20contigo." target="_blank"><img style="width: 35px;padding-bottom: 10px;padding-right: 10px;" src="https://alojacolombia.com/img/whatsapp.png"></a>
    </div>
</div>

@endcomponent