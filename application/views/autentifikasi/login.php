<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> THE VISIT </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="assets/css/style.css" rel="stylesheet">
  </head>
  <body>
    <div class="global-container">
      <div class="card login-form">
        <div class="card-body">
          <h1 class="card-title text-center"> L O G I N </h1>
        </div>
        <div class="card-text">
        <form class="user" method="post" action="<?= base_url('autentifikasi/login'); ?>">
          <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
          </div>
          <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" class="form-control" id="exampleInputPassword1">
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1">
            <label class="form-check-label" for="exampleCheck1">Check me out</label>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary"> Login </button>
          </div>
        </form>
        <form class="user" method="post" action="<?= base_url('autentifikasi/lupaPassword'); ?>">
          <div class="text-center">
            <a href="<?= base_url('autentifikasi'); ?>"> Forgot Password</a>
          </div>
        </form>
        <form class="user" method="post" action="<?= base_url('autentifikasi/registrasi'); ?>">
          <div class="text-center">
            <a href="<?= base_url('autentifikasi'); ?>"> Sign Up</a>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>