<ul class="nav nav-tabs" role="tablist">

<li <?php if (View::checkForActiveAction($filename, "index")) { echo ' class="active" ';  } ?> ><a href="<?php echo Config::get('URL')?>admin/index"><h4>All Candidate</h4></a></li>

<li <?php if (View::checkForActiveAction($filename, "allresult")) { echo ' class="active" ';  } ?>>
    <a href="<?php echo Config::get('URL')?>admin/allresult">
     <h4>View All Result</h4>
    </a>
  </li>
<!-- <li <?php if (View::checkForActiveAction($filename, "newcanform")) { echo ' class="active" ';  } ?>>
    <a href="<?php echo Config::get('URL')?>admin/newcanform">
     <h4>Add New Candidate</h4>
    </a>

  </li> -->
  <li <?php if (View::checkForActiveAction($filename, "batch")) { echo ' class="active" ';  } ?>>
    <a href="<?php echo Config::get('URL')?>admin/batch">
     <h4>Setting</h4>
    </a>

  </li>
  <li <?php if (View::checkForActiveAction($filename, "changepass")) { echo ' class="active" ';  } ?>>
    <a href="<?php echo Config::get('URL')?>admin/changepass">
     <h4>Change Password</h4>
    </a>

  </li>
<li class="">
    <a href="<?php echo Config::get('URL'); ?>login/logoutadmin">
     <h4>Logout</h4>
    </a>

  </li>



   
      </ul>   
        <?php
		
		/*  
	if (Session::userIsLoggedIn()){ ?>
		<?php if (View::checkForActiveController($filename, "login")) { echo ' class="active" '; } ?> 

        <?php } */ 
		
		?>
		