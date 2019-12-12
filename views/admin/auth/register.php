<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php asset('admin/images/favicon.ico'); ?>">

    <title>Fab Admin - Registration </title>

    <!-- Bootstrap 4.0-->
    <link rel="stylesheet" href="<?php asset('admin/assets/vendor_components/bootstrap/dist/css/bootstrap.min.css'); ?>">

    <!-- Bootstrap extend-->
    <link rel="stylesheet" href="<?php asset('admin/css/bootstrap-extend.css'); ?>">

    <!-- Theme style -->
    <link rel="stylesheet" href="<?php asset('admin/css/master_style.css'); ?>">

    <!-- Fab Admin skins -->
    <link rel="stylesheet" href="<?php asset('admin/css/skins/_all-skins.css'); ?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body class="hold-transition register-page">

<div class="container h-p100">
    <div class="row align-items-center justify-content-md-center h-p100">

        <div class="col-lg-4 col-md-8 col-12">
            <div class="register-box">
                <div class="register-box-body">
                    <h3 class="text-center">Присоединяйтесь</h3>
                    <p class="login-box-msg">Регистрация нового пользователя</p>

                    <form action="<?php route('admin.register.store', 'Register'); ?>" method="post">
                        <?php csrf_field(); ?>
                        <div class="form-group has-feedback">
                            <input type="text" name="name" value="<?php echo old('name'); ?>" class="form-control" placeholder="Полное имя">
                            <span class="ion ion-person form-control-feedback "></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="text" name="email" value="<?php echo old('email'); ?>" class="form-control" placeholder="Email">
                            <span class="ion ion-email form-control-feedback "></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" name="password" class="form-control" placeholder="Пароль">
                            <span class="ion ion-locked form-control-feedback "></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" name="password_confirm" class="form-control" placeholder="Повторите пароль">
                            <span class="ion ion-log-in form-control-feedback "></span>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="checkbox">
                                    <input type="checkbox" name="confirm" id="basic_checkbox_1" >
                                    <label for="basic_checkbox_1">Я согласен с <a href="#" class="text-warning"><b>условиями</b></a></label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info btn-block margin-top-10">Зарегестрироваться</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <div class="social-auth-links text-center">
                        <p>- ИЛИ -</p>
                        <a href="#" class="btn btn-outline btn-social-icon btn-light"><i class="fa fa-facebook"></i></a>
                        <a href="#" class="btn btn-outline btn-social-icon btn-light"><i class="fa fa-google-plus"></i></a>
                        <a href="#" class="btn btn-outline btn-social-icon btn-light"><i class="fa fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline btn-social-icon btn-light"><i class="fa fa-instagram"></i></a>
                    </div>
                    <!-- /.social-auth-links -->

                    <div class="margin-top-20 text-center">
                        <p>Уже есть аккаунт? <a href="login.html" class="text-info m-l-5">Войти</a></p>
                    </div>

                </div>
                <!-- /.form-box -->
                <?php if (has_errors()): ?>
                    <ul>
                        <?php foreach (errors() as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (has_info()): ?>
                    <ul>
                        <?php foreach (info() as $info): ?>
                            <li><?php echo $info; ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>


<!-- jQuery 3 -->
<script src="<?php asset('admin/assets/vendor_components/jquery/dist/jquery.min.js'); ?>"></script>

<!-- popper -->
<script src="<?php asset('admin/assets/vendor_components/popper/dist/popper.min.js'); ?>"></script>

<!-- Bootstrap 4.0-->
<script src="<?php asset('admin/assets/vendor_components/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>


</body>
</html>
