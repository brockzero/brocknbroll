<?php
require_once("form.php");
$form = new Form();
?>
<html>
    <head>
    </head>
    <body>
        <h2>Login</h2>
        <?php
        if($form->getNumErrors() > 0){
            echo '<p>'.$form->getNumErrors().' error(s) found</p>';
        }
        echo '<p>server: '.$_SERVER['SERVER_NAME']. ' request uri: ' .$_SERVER['REQUEST_URI'].'</p>';
        ?>
        <form action="LoginController.php" method="POST">
            <table>
                <tr>
                    <td>Username:</td>
                    <td><input type="text" name="user" maxlength="30" value="<?php echo $form->getValue("user"); ?>"></td>
                    <td><?php echo $form->getError("user"); ?></td>
                </tr>
                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="pass" maxlength="30" value="<?php echo $form->getValue("pass"); ?>"></td>
                    <td><?php echo $form->getError("pass"); ?></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="checkbox" name="remember" <?php if($form->getValue("remember") != ""){ echo "checked"; } ?>>
                    Remember me next time
                    <input type="hidden" name="sublogin" value="1">
                    <input type="submit" value="Login"></td>
                </tr>
                <tr>
                    <td colspan="2"><br>[<a href="admin/forgotpass.php">Forgot Password?</a>]</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </form>
    </body>
</html>