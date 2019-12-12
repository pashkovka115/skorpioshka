<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php asset('admin/images/favicon.ico'); ?>">

    <title>Fab Admin - Log in </title>

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
<body class="hold-transition login-page">

<div class="container h-p100">
    <div class="row align-items-center justify-content-md-center h-p100">
        <div class="col-lg-4 col-md-8 col-12">
            <div class="login-box">
                <div class="login-box-body">
                    <h3 class="text-center">Получите счастье вместе с нами</h3>
                    <p class="login-box-msg">Войти в свой аккаунт</p>

                    <form action="<?php route('admin.auth', 'Login'); ?>" method="post">
                        <?php csrf_field(); ?>
                        <div class="form-group has-feedback">
                            <input type="email" name="email" class="form-control rounded" placeholder="Email">
                            <span class="ion ion-email form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="password" name="password" class="form-control rounded" placeholder="Password">
                            <span class="ion ion-locked form-control-feedback"></span>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="checkbox">
                                    <input type="checkbox" name="remember" id="basic_checkbox_1" >
                                    <label for="basic_checkbox_1">Запомнить меня</label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-6">
                                <div class="fog-pwd text-right">
                                    <a href="javascript:void(0)" class="text-danger"><i class="ion ion-locked"></i> Забыли пароль?</a><br>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info btn-block margin-top-10">Войти</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <!--<div class="social-auth-links text-center">
                        <p>- OR -</p>
                        <a href="#" class="btn btn-outline btn-light btn-social-icon"><i class="fa fa-facebook"></i></a>
                        <a href="#" class="btn btn-outline btn-light btn-social-icon"><i class="fa fa-google-plus"></i></a>
                        <a href="#" class="btn btn-outline btn-light btn-social-icon"><i class="fa fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline btn-light btn-social-icon"><i class="fa fa-instagram"></i></a>
                    </div>-->
                    <!-- /.social-auth-links -->

                    <div class="margin-top-30 text-center">
                        <p>Нет аккаунта? <a href="register.html" class="text-warning ml-5">Зарегестрироваться</a></p>
                    </div>

                </div>
                <!-- /.login-box-body -->
            </div>
            <!-- /.login-box -->

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


<!-- jQuery 3 -->
<script src="<?php asset('admin/assets/vendor_components/jquery/dist/jquery.min.js'); ?>"></script>

<!-- popper -->
<script src="<?php asset('admin/assets/vendor_components/popper/dist/popper.min.js'); ?>"></script>

<!-- Bootstrap 4.0-->
<script src="<?php asset('admin/assets/vendor_components/bootstrap/dist/js/bootstrap.min.js'); ?>"></script>

</body>
</html>
