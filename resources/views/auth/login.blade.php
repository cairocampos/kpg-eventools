<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EvenTools - Login</title>
    <link rel="stylesheet" href="{{asset('css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>
<body>
    <div class="container">
        <div class="login row justify-content-center align-items-center">
            <div class="col-md-4">
                <h3>Autentique-se</i> </h3>
                <h1>Faça seu login na plataforma.</h1>
            </div>
            <div class="col-md-4">
                <form method="POST" action="{{route('login')}}" class="form form__login">
                    @csrf
                    <h1 class="text-secondary"><i class="fab fa-bandcamp"></i> EvenTools</h1>
                    <div class="form-group">
                        <div class="input-group flex-nowrap">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="addon-wrapping">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group flex-nowrap">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="addon-wrapping"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control" placeholder="Senha" aria-label="Senha" aria-describedby="addon-wrapping">
                        </div>
                        <a href="#">Esqueci minha senha</a>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-secondary">Entrar</button>
                    </div>
                    <p>Não possui cadastro ? <a href="{{route('register')}}">Cadastre-se</a></p>
                </form>
            </div>                
        </div>
    </div>
</body>
</html>