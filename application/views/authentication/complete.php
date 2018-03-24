<div>
    <div>
        <div id="navbar" class="navbar navbar-default">
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-lg-6">
                </div>

                <div class="col-lg-6">
                    <!-- PAGE CONTENT BEGINS -->
                    
                    <div class="col-lg-6 col-lg-offset-6">
                        <h2>Almost There!</h2>
                        <h5>Hello <span><?php echo $firstName; ?></span>. Your username is <span><?php echo $userName;?></span></h5>
                        <small>Please enter a password to begin using the site.</small>
                    <?php 
                        $fattr = array('class' => 'form-signin');
                        echo form_open(site_url().'triuneAuth/complete/token/'.$token, $fattr); ?>
                        <div class="form-group">
                        <?php echo form_password(array('name'=>'password', 'id'=> 'password', 'placeholder'=>'Password', 'class'=>'form-control', 'value' => set_value('password'))); ?>
                        <?php echo form_error('password') ?>
                        </div>
                        <div class="form-group">
                        <?php echo form_password(array('name'=>'passwordConfirmation', 'id'=> 'passwordConfirmation', 'placeholder'=>'Confirm Password', 'class'=>'form-control', 'value'=> set_value('passwordConfirmation'))); ?>
                        <?php echo form_error('passconf') ?>
                        </div> 
                        <?php echo form_submit(array('value'=>'Complete', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
                        <?php echo form_close(); ?>
   
                    </div>
                    <!-- PAGE CONTENT ENDS -->
                    </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->

    </div>
</div><!-- /.main-content -->