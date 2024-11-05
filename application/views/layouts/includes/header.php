<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Starter Template · Bootstrap</title>

    <!-- Bootstrap core CSS -->
 <link href="<?php echo base_url();?>assets/css/bootstrap.css" rel="stylesheet">

    <!-- Favicons -->
<meta name="theme-color" content="#563d7c">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url();?>assets/css/custom.css" rel="stylesheet">
  </head>
      <body>
      <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="<?php echo base_url();?>">The Gaming Place</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>


      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="<?php echo base_url();?>">Home</a>
          </li>
          <?php if(!$this->session->userdata('logged_in')):?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo base_url();?>users/register">Create An Account</a>
          </li>
        <?php endif;?>
        </ul>
        <?php if(!$this->session->userdata('logged_in')):?>
        <form class="form-display" method="post" action="<?php echo base_url();?>users/login">
          <div class="form-group" style="margin: 0;">
            <input name="username"type="text" placeholder="Enter username" class="form-control">
          </div>
          <div class="form-group mx-2" style="margin:0;">
            <input name="password"type="password" placeholder="Enter password" class="form-control">
          </div>
          <button name="submit" type="submit" class="btn btn-primary mx-2">Login</button>
        </form>
        <?php else :?>
            <form class="form-display" method="post" action="<?php echo base_url();?>users/logout">
            <button name="submit" type="submit" class="btn btn-primary mx-2">Logout</button>
          <?php endif;?>
        
      </div>

    </nav>
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <?php $this->load->view("layouts/includes/sidebar");?>
          </div>
          
        </div>
        <div class="col-md-8">
            <div class="card">
            <div class="card-header bg-success text-white">
              <h3 class="card-title">
                Latses Release
              </h3>
            </div>
            <div class="card-body">