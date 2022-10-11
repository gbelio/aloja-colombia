<div style="display: flex;align-content: center;align-items: center;justify-content: center; height:90vh">
    <div style="text-align: center">
        <img src="{{ url('/favicon.png') }}" alt="">
        <h1>Se ha producido un error, vuelve a intenarlo!</h1>
        <a style="text-decoration: none;border: 1px solid red;border-radius: 5px;padding: 10px 100px;color: black;font-weight: bold;" 
            href="{{ url()->previous() }}" role="button">
            VOLVER
        </a>
    </div>
</div>
<style>
    @media (min-width: 575px) {
        h1{
            font-size: 5em;
        }
        a{
            border: 4px solid red !important;
            border-radius: 10px !important;
            font-size: 3em;
        }
    }
</style>