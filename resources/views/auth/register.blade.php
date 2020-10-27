<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cadastro | EvenTools</title>
    <link rel="stylesheet" href="{{asset('css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/alertify.min.css')}}">
</head>
<body>
    <div class="container" id="app">
        <div class="login row justify-content-center align-items-center">
            <div class="col-md-6">
                <h3>Cadastre-se</i> </h3>
                <h1><strong>EvenTools</strong> é uma plataforma para criação e organização de eventos online.</h1>
                <p>Chame a galera para curtir seus eventos de forma simples.</p>
                <a href="{{route('login')}}"><i class="fas fa-arrow-left"></i> Voltar ao login</a>
            </div>
            <div class="col-md-4">
                <form method="POST" action="{{route('register')}}" class="form form__login">
                    @csrf
                    <h1 class="text-secondary"><i class="fab fa-bandcamp"></i> Crie sua conta</h1>
                    <div class="form-group">
                        <div class="input-group flex-nowrap">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="text" name="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group flex-nowrap">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="name" class="form-control" placeholder="Nome" aria-label="Username" aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group flex-nowrap">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control" placeholder="Senha" aria-label="Username" aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group flex-nowrap">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirme sua senha" aria-label="Username" aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-secondary">Cadastrar</button>
                    </div>
                    <p>Já possui cadastro ? <a href="{{route('login')}}">Acessar</a></p>
                </form>
            </div>                
        </div>
    </div>

    <script src="{{'js/alertify.min.js'}}"></script>
    <script>
        let errors = {!! json_encode($errors->all()) !!}
        if(errors.length) {
            for(const error of errors) {
                alertify.error(error);
            }
        }        
    </script>
</body>
</html>