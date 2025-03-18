<?php include "./components/header.php";?>




    <div class="hero">
        <div class="hero-content">
            <h1>Welcome to our PHP App</h1>
            <p>Securely login and manage your account with us</p>
            <div class="hero-buttons">

            <?php if(isLoggedIn()) : ?> 
                    <a class="btn" href="app.php">App</a>
                    <a class="btn" href="logout.php">Logout</a>
            
            <?php else:  ?>
                <a class="btn" href="login.php">Login</a>
                <a class="btn" href="register.php">Register</a>

            <?php endif; ?>
        </div>
        </div>
    </div>


<?php include "./components/footer.php"; ?>



