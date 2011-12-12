<?php
/**
 * Administrative login.
 * 
 * The purpose of this page is to provide access to the standard Mahara login procedure
 * when it is removed from the front page and when Shibboleth is not available.
 * So that admin can still have access to the site.
 * 
 * Note that, in order to connect, users must have a valid account that will be recognized by
 * one of the predefined authentication providers. In most cases that means to have a valid 
 * internal account.
 *
 * Captures username and password then delegates to the standard Mahara login procedure.
 *
 */
define('INTERNAL', 1);
define('PUBLIC', 1);

require_once dirname(__FILE__) . '/../../../init.php';
require_once dirname(__FILE__) . '/../lib.php';

$username = isset($_POST['login_username']) ? $_POST['login_username'] : '';
$password = isset($_POST['login_password']) ? $_POST['login_password'] : '';
$message = '';

if (!empty($username) && !empty($password)) {
    try {
        if ($USER->login($username, $password)) {
            redirect();
        } else {
            $message = 'login failed';
        }
    } catch (Exception $e) {
        $message = 'login failed';
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
        <title>Mahara</title>
    </head>
    <body>

        <h1>Admin Login</h1>
        <div><?php echo $message; ?></div>

        <form class="pieform" name="login" method="post" action="" id="login">
            <table cellspacing="0">
                <tbody>
                    <tr id="login_login_username_container" class="required text">
                        <th><label for="login_login_username">Username</label> <span
                                class="requiredmarker">*</span></th>
                        <td><input type="text" class="required text autofocus"
                                   id="login_login_username" name="login_username" tabindex="2"
                                   value="<?php echo $username; ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="description"></td>
                    </tr>
                    <tr id="login_login_password_container" class="password">
                        <th><label for="login_login_password">Password</label></th>
                        <td><input type="password" class="password" id="login_login_password"
                                   name="login_password" tabindex="2" value=""></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="description"></td>
                    </tr>
                    <tr id="login_submit_container" class="submit">
                        <th></th>
                        <td><input type="submit" class="submit" id="login_submit"
                                   name="submit" tabindex="2" value="Connect"></td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" class="hidden" id="login_login_submitted"
                   name="login_submitted" value="1"><input type="hidden" class="hidden"
                   id="login_sesskey" name="sesskey" value=""><input type="hidden"
                   name="pieform_login" value=""></form>
    </body>
</html>
