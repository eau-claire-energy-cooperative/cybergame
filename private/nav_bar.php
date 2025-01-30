<?php session_start();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href=<?php echo url_for('pick_passcode'); ?>>Galactic Codebreaker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Menu</a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="<?php echo url_for('pick_passcode'); ?>">Guess a Passcode</a></li>
                        <li><a class="dropdown-item" href="<?php echo url_for('view_passcode'); ?>">View my Statistics</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="<?php echo url_for('tutorial'); ?>">Tutorial</a></li>
                        <li><a class="dropdown-item" href="<?php echo url_for('leaderboard'); ?>">Leaderboard</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <?php
                        $loggedIn = checkSession();
                        if($loggedIn){ ?>
                          <li><a class="dropdown-item" href="<?php echo url_for('private/logout.php'); ?>">Log Out</a></li>
                        <?php }else{ ?>
                          <li><a class="dropdown-item" href="<?php echo url_for('login'); ?>">Log In</a></li>
                        <?php }
                        ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
