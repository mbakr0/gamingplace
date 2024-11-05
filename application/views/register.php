              <?php echo validation_errors('<div class="alert alert-danger">','</div>');?>
              <form role="form" method="post" action="<?php echo base_url();?>users/register">
                <div class="form-group">
                  <label>First Name*</label>
                  <input type="text" name="first_name" class="form-control" placeholder="Enter your First Name">
                </div>
                <div class="form-group">
                  <label>Last Name*</label>
                  <input type="text" name="last_name" class="form-control" placeholder="Enter your last Name">
                </div>
                <div class="form-group">
                  <label>Email Eddress*</label>
                  <input type="email" name="email" class="form-control" placeholder="Enter your email">
                </div>
                <div class="form-group">
                  <label>Chose Username*</label>
                  <input type="text" name="username" class="form-control" placeholder="Enter your username">
                </div>
                <div class="form-group">
                  <label>Password*</label>
                  <input type="password" name="password" class="form-control" placeholder="Enter your password">
                </div>
                <div class="form-group">
                  <label>Confirm Password*</label>
                  <input type="password" name="password2" class="form-control" placeholder="Enter your password">
                </div>
                <input type="submit" name="submit" value="Register" class="btn btn-primary">
              </form>
      