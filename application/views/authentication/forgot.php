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
                        <h2>Forgot Password</h2>
                        <p>Please enter your email address and we'll send you instructions on how to reset your password</p>
                        <?php $fattr = array('class' => 'form-signin');
                            echo form_open(site_url().'auth/forgot/', $fattr); ?>
                        <div class="form-group">
                        <?php echo form_input(array(
                            'name'=>'emailAddress', 
                            'id'=> 'emailAddress', 
                            'placeholder'=>'Email Address', 
                            'class'=>'form-control', 
                            'value'=> set_value('emailAddress'))); ?>
                        <?php echo form_error('emailAddress') ?>
                        </div>
                        <?php echo form_submit(array('value'=>'Submit', 'class'=>'btn btn-lg btn-primary btn-block')); ?>
                        <?php echo form_close(); ?>    
                    </div>

                    <!-- PAGE CONTENT ENDS -->
                
                    <div >
                        <?php
                            if ($this->session->flashdata('msg')){ //change!
                                echo "<div class='message' style='color:red'>";
                                echo $this->session->flashdata('msg');
                                echo "</div>";
                            }
                        ?>
                    </div>
                    
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.page-content -->

    </div>
</div><!-- /.main-content -->