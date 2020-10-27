@extends("layouts.app")

@section("content")
    <section class="expired">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="{{asset('images/undraw_feeling_blue_4b7q.svg')}}" alt="Expired" width="100%">
                </div>
                <div class="col-md-6">
                    <h1 class="text-primary">Ooops...</h1>
                    <h3 class="text-secondary">
                        Esse evento não está disponível..
                    </h3>
                    <ul>
                        <li>O evento pode ter sido cancelado pelo organizador</li>
                        <li>Você não participa dessa organização</li>
                    </ul>
                    <a href="/">Voltar a home</a>
                </div>
            </div>
        </div>
    </section>
@endsection