<div class="container">
    <h1>UJIAN PSIKOMETRIK</h1>

    <div class="box">
        <h2>Your profile</h2>

        <!-- echo out the system feedback (error and success messages) -->
        <?php $this->renderFeedbackMessages(); ?>

        <div>Your username: <?= $this->user_name; ?></div>
        <div>Your email: <?= $this->user_email; ?></div>
        
        <a href="<?php echo Config::get('URL')?>test"><h2>MULAKAN UJIAN SEKARANG</h2></a>
    </div>
</div>
