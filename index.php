<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

// Report all PHP errors
error_reporting(E_ERROR);

// Path definitions
define("IMAGES", "images/");
define("INCLUDES", "includes/");
define("MODULES", "modules/");

define("CONFIG_FILE", "includes/config.inc.php");

require_once("includes/functions.php");
require_once("includes/helpers.php");
require_once("includes/html_functions.php");

// Start the session valid for opengamepanel_web only
startSession();

// Useful for debugging :)
// echo "<p>Session ID is " . session_id() . "</p>";
// echo "<p>Lifetime is: " . $cookie_lifetime . "<br />Dir is " . rtrim(dirname($_SERVER["SCRIPT_NAME"]),"/") . "/" . "<br /> Session cookie domain path is " . $session_cookie_domain_path . "<br />SSL is " . $ssl . "</p>";

//Config Check
$config_inc_readable = is_readable(CONFIG_FILE);
if (!$config_inc_readable && file_exists("install.php")) {
	header('Location: install.php');
	exit();
}
if ('' == file_get_contents(CONFIG_FILE)) {
	header('Location: install.php');
	exit();
}

require_once CONFIG_FILE;
// Connect to the database server and select database.
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);

// Load languages.
include_once("includes/lang.php");

if (!$db instanceof OGPDatabase) {
	ogpLang();
	die(get_lang('no_db_connection'));
}

// Logged in user settings - access this global variable where needed
if (hasValue($_SESSION['user_id'])) {
	$loggedInUserInfo = $db->getUserById($_SESSION['user_id']);
}

$settings = $db->getSettings();
@$GLOBALS['panel_language'] = $settings['panel_language'];
ogpLang();

require_once("includes/view.php");
$view = new OGPView();
$view->setCharset(get_lang('lang_charset'));
if (isset($_GET['type']) && $_GET['type'] == 'cleared') {
	heading(true);
	$view->printView(true);
} else {
	ogpHome();
	$view->printView();
}

function heading()
{

	global $db, $view, $settings;

	$view->setCharset(get_lang('lang_charset'));
	$view->setTimeZone($settings['time_zone']);

	if (!file_exists(CONFIG_FILE)) {
		print_failure(get_lang("failed_to_read_config"));
		$view->refresh("index.php");
		return;
	}
	// Start Output Buffering

	if (isset($settings['maintenance_mode']) && $settings['maintenance_mode'] == "1") {
		if ($_SESSION['users_group'] != "admin") {
			echo "<h2>" . $settings['maintenance_title'] . "</h2>";
			echo "<p>" . $settings['maintenance_message'] . "</p>";
			$view->setTitle("OGP: Maintenance.");
			echo "<p class='failure'>" . get_lang("logging_out_10") . "...</p>";
			$view->refresh("index.php", 10);
			session_destroy();
			return;
		}
	}
	include "includes/navig.php";
	if (isset($maintenance)) echo $maintenance;
}

function ogpHome()
{
	global $db, $view, $settings;

	if (isset($_GET['lang']) and $_GET['lang'] != "-")
		$lang = $_GET['lang'];
	elseif (isset($settings['panel_language']))
		$lang = $settings['panel_language'];
	else
		$lang = "English";

	$locale_files = makefilelist("lang/", ".|..|.svn", true, "folders");
	$lang_sel = "<select name='lang' onchange=\"this.form.submit();\" >\n" .
		"<option>-</option>\n";
	for ($i = 0; $i < count($locale_files); $i++) {
		$selected = (isset($_GET['lang']) and $_GET['lang'] != "-" and $_GET['lang'] == $locale_files[$i]) ? "selected='selected'" : "";
		$lang_sel .= "<option $selected value='" . $locale_files[$i] . "' >" . $locale_files[$i] . "</option>\n";
	}
	$lang_sel .= "</select>\n";
	$lang_switch = (isset($_GET['lang']) and $_GET['lang'] != "-") ? "&amp;lang=" . $_GET['lang'] : "";
?>


	%top%
	<div class="menu-bg">
		<div class="menu">
			<ul>
				<li><a href="index.php<?php echo preg_replace("/\&amp;/", "?", $lang_switch); ?>" <?php if (!isset($_GET['m'])) echo 'class="admin_menu_link_selected"';
																									else echo 'class="admin_menu_link"'; ?> target="_self"><span class="controlpanellogin"><?php echo get_lang("login_title"); ?></span></a></li>
				<?php
				$menus = $db->getMenusForGroup('guest');
				if (!empty($menus)) {
					foreach ($menus as $menu) {
						$module = $menu['module'];
						if (!empty($menu['subpage'])) {
							$subpage = "&amp;p=" . $menu['subpage'];
							$button = $menu['subpage'];
							if (isset($_GET['p']) and $_GET['p'] == $menu['subpage']) $menu_link_class = 'user_menu_link_selected';
							else $menu_link_class = 'user_menu_link';
						} else {
							$subpage = "";
							$button = $menu['module'];
							if (isset($_GET['m']) and $_GET['m'] == $menu['module']) $menu_link_class = 'user_menu_link_selected';
							else $menu_link_class = 'user_menu_link';
						}

						$button_url = "?m=" . $module . $subpage . $lang_switch;

						if (preg_match('/\\_?\\_/', get_lang("$button"))) {
							$button_name = $menu['menu_name'];
						} else {
							$button_name = get_lang("$button");
						}

						echo "<li><a class='" . $menu_link_class . "' href='" . $button_url . "'><span class='$button'>$button_name</span></a>
				  </li>\n";
					}
				}
				?>
			</ul>
		</div>
	</div>
	%topbody%
	<?php
	if (isset($_GET['m'])) {
		heading();
		//tagged for future use...
		/*
			$postdata = "";
			foreach($_POST as $key =>$value)
				$postdata .= ",'$key': '$value'";
			$postdata = substr($postdata,1);
			$postdata = "{".$postdata."}";
		*/
	} else {
		$default_page = $db->isModuleInstalled('dashboard') ? "m=dashboard&amp;p=dashboard" : "m=gamemanager&p=game_monitor";
		if (isset($_SESSION['users_login'])) {
			$userInfo = $db->getUser($_SESSION['users_login']);
			if (isset($_SESSION['users_passwd']) and !empty($_SESSION['users_passwd']) and $_SESSION['users_passwd'] == $userInfo['users_passwd']) {
				print_success(get_lang("already_logged_in_redirecting_to_dashboard") . ".");
				$view->refresh("home.php?$default_page", 2);
				echo "%botbody%
					  %bottom%";
				return;
			}
		}

		if (isset($_POST['login'])) {
			$client_ip = getClientIPAddress();

			$ban_list = $db->resultQuery("SHOW TABLES LIKE 'OGP_DB_PREFIXban_list';");
			if (empty($ban_list)) {
				$db->query("CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXban_list` (
							`client_ip` varchar(255) NOT NULL,
							`logging_attempts` int(11) NOT NULL DEFAULT '0',
							`banned_until` varchar(16) NOT NULL DEFAULT '0',
							 PRIMARY KEY (`client_ip`)
							) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
			}

			$banlist_info = $db->resultQuery("SELECT logging_attempts, banned_until FROM `OGP_DB_PREFIXban_list` WHERE client_ip='" . $client_ip . "';");
			$login_attempts = !$banlist_info ? 0 : $banlist_info['0']['logging_attempts'];

			if ($banlist_info and $banlist_info['0']['banned_until'] > 0 and $banlist_info['0']['banned_until'] <= time()) {
				$db->query("DELETE FROM `OGP_DB_PREFIXban_list` WHERE client_ip='$client_ip';");
				$login_attempts = 0;
			}

			if ($login_attempts == $settings["login_attempts_before_banned"]) {
				print_failure("Banned until " . date("r", $banlist_info['0']['banned_until']));
				echo "%botbody%
					  %bottom%";
				return;
			}

			$userInfo = $db->getUser($_POST['ulogin']);

			// If result matched $myusername and $mypassword, table row must be 1 row
			if (isset($userInfo['users_passwd']) && md5($_POST['upassword']) == $userInfo['users_passwd']) {
				// Handle recaptcha if enabled
				// But admins don't have to do this :)
				if ($settings['recaptcha_use_login'] == "1" && !empty($settings['recaptcha_site_key']) && !empty($settings['recaptcha_secret_key']) && $userInfo['users_role'] != "admin") {
					$gRecaptchaResponse = sanitizeInputStr($_REQUEST['g-recaptcha-response']);

					$sitekey = $settings['recaptcha_site_key'];
					$secretkey = $settings['recaptcha_secret_key'];

					require_once('includes/classes/recaptcha/autoload.php');
					$recaptcha = new \ReCaptcha\ReCaptcha($secretkey);
					$resp = $recaptcha->verify($gRecaptchaResponse, $client_ip);

					if (empty($gRecaptchaResponse) || !$resp->isSuccess()) {
						print_failure("Recaptcha failed. Try again!");
						$view->refresh("index.php", 5);
						return;
					}
				}

				$_SESSION['user_id'] = $userInfo['user_id'];
				$_SESSION['users_login'] = $userInfo['users_login'];
				$_SESSION['users_email'] = $userInfo['users_email'];
				$_SESSION['users_passwd'] = $userInfo['users_passwd'];
				$_SESSION['users_group'] = $userInfo['users_role'];
				$_SESSION['users_lang'] = isset($_GET['lang']) ? $_GET['lang'] : $userInfo['users_lang'];
				$_SESSION['users_theme'] = $userInfo['users_theme'];
				$_SESSION['users_api_key'] = $db->getApiToken($userInfo['user_id']);
				print_success(get_lang("logging_in") . "...");
				$db->logger(get_lang("logging_in") . "...");
				$db->query("DELETE FROM `OGP_DB_PREFIXban_list` WHERE client_ip='$client_ip';");
				$view->refresh("home.php?$default_page", 2);
			} else {
				print_failure(get_lang("bad_login"));
				$login_attempts++;
				if ($login_attempts == $settings["login_attempts_before_banned"]) {
					$banned_until = time() + (array_key_exists("login_ban_time", $settings) && !empty($settings["login_ban_time"]) && is_numeric($settings["login_ban_time"]) ? $settings["login_ban_time"] : 300); // Five minutes or user defined setting.

					if (!$banlist_info)
						$db->query("INSERT INTO `OGP_DB_PREFIXban_list` (`client_ip`) VALUES('$client_ip');");

					$db->logger(get_lang("bad_login") . " ( Banned until " . date("r", $banned_until) . " ) [ " . get_lang("login") . ": " . sanitizeInputStr($_POST["ulogin"]) . ", " . get_lang("password") . ": ******** ]");
					$db->query("UPDATE `OGP_DB_PREFIXban_list` SET logging_attempts='$login_attempts', banned_until='$banned_until' WHERE client_ip='$client_ip';");
					print_failure("Banned until " . date("r", $banned_until));
				} else {
					if (!$banlist_info)
						$db->query("INSERT INTO `OGP_DB_PREFIXban_list` (`client_ip`) VALUES('$client_ip');");

					$db->logger(get_lang("bad_login") . " ( $login_attempts ) [ " . get_lang("login") . ": " . sanitizeInputStr($_POST["ulogin"]) . ", " . get_lang("password") . ": ******** ]");
					$db->query("UPDATE `OGP_DB_PREFIXban_list` SET logging_attempts='$login_attempts' WHERE client_ip='$client_ip';");
					$view->refresh("index.php", 2);
				}
			}
			echo "%botbody%
				  %bottom%";
			return;
		}
	?>
		<script>
			/*!
// Snow.js - v0.0.3
// kurisubrooks.com
*/

			// Amount of Snowflakes
			var snowMax = 35;

			// Snowflake Colours
			var snowColor = ["#DDD", "#EEE"];

			// Snow Entity
			var snowEntity = "&#x2022;";

			// Falling Velocity
			var snowSpeed = 0.75;

			// Minimum Flake Size
			var snowMinSize = 8;

			// Maximum Flake Size
			var snowMaxSize = 24;

			// Refresh Rate (in milliseconds)
			var snowRefresh = 30;

			// Additional Styles
			var snowStyles = "cursor: default; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; -o-user-select: none; user-select: none;";

			/*
			// End of Configuration
			// ----------------------------------------
			// Do not modify the code below this line
			*/

			var snow = [],
				pos = [],
				coords = [],
				lefr = [],
				marginBottom,
				marginRight;

			function randomise(range) {
				rand = Math.floor(range * Math.random());
				return rand;
			}

			function initSnow() {
				var snowSize = snowMaxSize - snowMinSize;
				marginBottom = document.body.scrollHeight - 5;
				marginRight = document.body.clientWidth - 15;

				for (i = 0; i <= snowMax; i++) {
					coords[i] = 0;
					lefr[i] = Math.random() * 15;
					pos[i] = 0.03 + Math.random() / 10;
					snow[i] = document.getElementById("flake" + i);
					snow[i].style.fontFamily = "inherit";
					snow[i].size = randomise(snowSize) + snowMinSize;
					snow[i].style.fontSize = snow[i].size + "px";
					snow[i].style.color = snowColor[randomise(snowColor.length)];
					snow[i].style.zIndex = 1000;
					snow[i].sink = snowSpeed * snow[i].size / 5;
					snow[i].posX = randomise(marginRight - snow[i].size);
					snow[i].posY = randomise(2 * marginBottom - marginBottom - 2 * snow[i].size);
					snow[i].style.left = snow[i].posX + "px";
					snow[i].style.top = snow[i].posY + "px";
				}

				moveSnow();
			}

			function resize() {
				marginBottom = document.body.scrollHeight - 5;
				marginRight = document.body.clientWidth - 15;
			}

			function moveSnow() {
				for (i = 0; i <= snowMax; i++) {
					coords[i] += pos[i];
					snow[i].posY += snow[i].sink;
					snow[i].style.left = snow[i].posX + lefr[i] * Math.sin(coords[i]) + "px";
					snow[i].style.top = snow[i].posY + "px";

					if (snow[i].posY >= marginBottom - 2 * snow[i].size || parseInt(snow[i].style.left) > (marginRight - 3 * lefr[i])) {
						snow[i].posX = randomise(marginRight - snow[i].size);
						snow[i].posY = 0;
					}
				}

				setTimeout("moveSnow()", snowRefresh);
			}

			for (i = 0; i <= snowMax; i++) {
				document.write("<span id='flake" + i + "' style='" + snowStyles + "position:absolute;top:-" + snowMaxSize + "'>" + snowEntity + "</span>");
			}

			window.addEventListener('resize', resize);
			window.addEventListener('load', initSnow);
		</script>
		<style>
			html,
			body {
				margin: 0;
				height: 100%;
			}

			body {
				background: #182124
			}
		</style>

		<!-- Made for Revolution Theme v2 -->
		<style type="text/css">
			div.main-content {
				background: transparent;
				border: none;
				padding: 0;
				border-radius: 0px;
				-moz-border-radius: 0px;
			}
		</style>

		<table style='width:200px' align='center'>
			<tr style='background-color:transparent;'>
				<td style='background-color:transparent;'>
					<div class='bloc'>
						<h4>Chabka Hosting - Login</h4>
						<br>
						<div class="imageINfo">
							<img class="image" src="https://media.discordapp.net/attachments/1053740728590807070/1056280389146116117/chat.png">
						</div>
						<form action="index.php<?php echo preg_replace("/\&amp;/", "?", $lang_switch); ?>" name="login_form" method="post">
							<table>
								<tr>
									<td></td>
								</tr>
								<tr>
									<td><?php print_lang('login'); ?>:</td>
									<td class="iconLogo">
										<i class="fa fa-user fa-2x"></i>
										<input type="text" name="ulogin" id="ulogin" value="" size="20" />
									</td>
								</tr>
								<tr>
									<td><?php print_lang('password'); ?>:</td>
									<td class="iconLogo">
									<i class="fa-solid fa-lock"></i>

										</i><input type="password" name="upassword" id="upassword" value="" size="20" />
									</td>
								</tr>
								<?php
								if ($settings['recaptcha_use_login'] == "1" && !empty($settings['recaptcha_site_key']) && !empty($settings['recaptcha_secret_key'])) {
								?>
									<tr>
										<td><?php print_lang('solve_captcha'); ?>:</td>
										<td>
											<script src="https://www.google.com/recaptcha/api.js"></script>
											<div style="display: inline-block;" class="g-recaptcha" data-sitekey="<?php echo $settings['recaptcha_site_key']; ?>"></div>
										</td>
									</tr>
								<?php
								}
								?>
								<tr>
									<td><input type="submit" name="login" value="<?php print_lang('login_button'); ?>" /></td>
									<td></td>
								</tr>
							</table>
						</form>
						<script language="JavaScript">
							document.login_form.ulogin.focus();
						</script>
						<br>
					</div>
				</td>
			</tr>
		</table>
	<?php
	}
	?>
	<div class="clear"></div>
	%botbody%
	%bottom%
<?php
}
?>