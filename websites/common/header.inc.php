<?php
require_once(dirname(__FILE__)."/user_init.inc.php");

$is_fools_day = (date('d')==28 && date('m')==12);
$site_theme='dark';
$is_hentai_site=!empty($_GET['hentai']);
if (!empty($user)) {
	$site_theme=($user['site_theme']==1 ? 'light' : 'dark');
} else if (!empty($_COOKIE['site_theme']) && $_COOKIE['site_theme']=='light') {
	$site_theme='light';
}
?>
<!DOCTYPE html>
<html lang="ca" class="theme-<?php echo ($site_theme=='light') ? 'light' : 'dark'; ?><?php echo $is_hentai_site ? ' subtheme-hentai' : ''; ?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="theme-color" content="#000000">
		<meta name="referrer" content="origin">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="og:title" content="<?php echo !empty($page_title) ? htmlentities($page_title).' | '.htmlentities($site_config['site_title']) : htmlentities($site_config['site_title']); ?>">
		<meta property="og:url" content="<?php echo !empty($social_url) ? $site_config['base_url'].$social_url : $site_config['base_url']; ?>">
		<meta property="og:description" content="<?php echo !empty($social_description) ? htmlentities($social_description) : htmlentities($site_config['site_description']); ?>">
		<meta property="og:image" content="<?php echo !empty($social_image_url) ? $social_image_url : $site_config['preview_image']; ?>">
		<meta property="og:image:type" content="image/jpeg">
		<title><?php echo !empty($page_title) ? htmlentities($page_title).' | '.htmlentities($site_config['site_title']) : htmlentities($site_config['site_title']); ?></title>
		<link rel="icon" href="/favicon.png">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
<?php
if ($style_type=='catalogue') {
?>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.1/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css">
		<link rel="stylesheet" href="/js/videojs/video-js.min.css?v=<?php echo $version; ?>">
		<link rel="stylesheet" href="/js/videojs/videojs-chromecast.css?v=<?php echo $version; ?>">
<?php
}
if ($style_type=='news') {
?>
		<link rel="stylesheet" href="/style/magnific-popup-1.1.0.css">
<?php
}
?>
		<link rel="stylesheet" href="<?php echo $static_url; ?>/common/style/common.css?v=<?php echo $version; ?>">
		<link rel="stylesheet" href="/style/<?php echo $site_config['own_css']; ?>?v=<?php echo $version; ?>">
<?php
if ($is_fools_day){
?>
		<link rel="stylesheet" href="<?php echo $static_url; ?>/common/style/28dec.css?v=<?php echo $version; ?>" />
<?php
}
?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
		<script src="<?php echo $static_url; ?>/common/js/common.js?v=<?php echo $version; ?>"></script>
		<script src="/js/<?php echo $site_config['own_js']; ?>?v=<?php echo $version; ?>"></script>
<?php
if ($style_type=='catalogue') {
?>
		<script>
			window.SILVERMINE_VIDEOJS_CHROMECAST_CONFIG = {
				preloadWebComponents: true,
			};
		</script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.1/jquery-ui.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
		<script src="/js/megajs/main.browser-umd.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videojs/video.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videostream.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videojs/lang_ca.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videojs/videojs-chromecast.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videojs/videojs-youtube.min.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videojs/videojs-landscape-fullscreen.min.js?v=<?php echo $version; ?>"></script>
		<script src="/js/videojs/videojs-hotkeys.min.js?v=<?php echo $version; ?>"></script>
		<script src="/js/double-slider.js?v=<?php echo $version; ?>"></script>
		<script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
<?php
}
if ($style_type=='news') {
?>
		<script src="/js/jquery.magnific-popup-1.1.0.min.js"></script>
<?php
}
?>
	</head>
	<body class="style-type-<?php echo $style_type; ?><?php echo !empty($extra_body_class) ? ' '.$extra_body_class : ''; ?>">
		<div class="main-container<?php echo ($style_type=='login' || $style_type=='text' || $style_type=='contact') ? ' obscured-background' : ''; ?>">
<?php
if ($style_type=='login') {
?>
			<div class="overlay-page">
				<div class="login-page">
					<div class="login-explanation">
						<div class="login-header">Registra’t a Fansubs.cat</div>
						<div class="login-points">
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-bars-staggered"></div>
								<div class="login-point-text">Podràs desar el contingut<br>en una llista personal</div>
							</div>
							<div class="login-point">
								<div class="login-point-text">Rebràs recomanacions<br>personalitzades</div>
								<div class="login-point-icon far fa-fw fa-thumbs-up"></div>
							</div>
							<div class="login-point">
								<div class="login-point-icon far fa-fw fa-star-half-stroke"></div>
								<div class="login-point-text">Podràs valorar la qualitat<br>de les traduccions i edicions</div>
							</div>
							<div class="login-point">
								<div class="login-point-text">Tindràs el progrés sincronitzat<br>a tots els dispositius</div>
								<div class="login-point-icon fas fa-fw fa-house-laptop"></div>
							</div>
						</div>
					</div>
					<div class="login-form<?php echo $style_type=='reset_password' ? ' hidden' : ''; ?>">
						<div class="login-form-main">
							<div class="login-subheader">Inicia la sessió</div>
							<form id="login-form" onsubmit="return login();" autocomplete="off" novalidate>
								<label for="login_username">Nom d'usuari</label>
								<input id="login_username" type="text" oninput="removeValidation(this.id);">
								<div id="login_username_validation" class="validation-message"></div>
								<label for="login_password">Contrasenya</label>
								<input id="login_password" type="password" oninput="removeValidation(this.id);">
								<div id="login_password_validation" class="validation-message"></div>
								<div id="login_generic_validation" class="validation-message-generic"></div>
								<button id="login_submit" type="submit" class="login-button">Inicia la sessió</button>
								<a class="forgot-password" onclick="showForgotPassword();">He oblidat la contrasenya</a>
							</form>
						</div>
						<div class="login-footer">Encara no n’ets membre? <a onclick="showRegister();">Registra-t’hi</a></div>
					</div>
					<div class="reset-password-form<?php echo $style_type!='reset_password' ? ' hidden' : ''?>">
						<div class="login-form-main">
							<div class="login-subheader">Restableix la contrasenya</div>
							<form id="reset-password-form" onsubmit="return resetPassword();" autocomplete="off" novalidate>
								<label for="reset_password">Contrasenya nova</label>
								<input id="reset_password" type="password" oninput="removeValidation(this.id);">
								<div id="reset_password_validation" class="validation-message"></div>
								<label for="reset_repeat_password">Repeteix la contrasenya</label>
								<input id="reset_repeat_password" type="password" oninput="removeValidation(this.id);">
								<div id="reset_repeat_password_validation" class="validation-message"></div>
								<div id="reset_generic_validation" class="validation-message-generic"></div>
								<input id="reset_username" type="hidden" value="<?php echo !empty($_GET['usuari']) ? htmlspecialchars($_GET['usuari']) : ''; ?>">
								<input id="reset_code" type="hidden" value="<?php echo !empty($_GET['codi']) ? htmlspecialchars($_GET['codi']) : ''; ?>">
								<button id="reset_submit" type="submit" class="login-button">Restableix i inicia la sessió</button>
							</form>
						</div>
						<div class="login-footer">L’has recordada? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="forgot-password-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Contrasenya oblidada</div>
							<form id="forgot-password-form" onsubmit="return forgotPassword();" autocomplete="off" novalidate>
								<label for="forgot_email">Adreça electrònica</label>
								<input id="forgot_email" type="email" oninput="removeValidation(this.id);">
								<div id="forgot_email_validation" class="validation-message"></div>
								<div id="forgot_generic_validation" class="validation-message-generic"></div>
								<button id="forgot_submit" type="submit" class="login-button">Envia’m un correu per a restablir-la</button>
							</form>
						</div>
						<div class="login-footer">T’has equivocat? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="forgot-password-result-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Comprova el correu</div>
							<div class="forgot-password-result-text">Si tenies un compte amb aquesta adreça electrònica, has d’haver rebut un correu electrònic amb informació sobre com restablir la contrasenya. Segueix-ne les instruccions.<br><br>Si no l’has rebut, comprova la carpeta del correu brossa i revisa que hagis introduït correctament l’adreça electrònica del compte.</div>
						</div>
						<div class="login-footer">Ja has canviat la contrasenya? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="register-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Registre</div>
							<form id="register-form" onsubmit="return register();" autocomplete="off" novalidate>
								<label for="register_username">Nom d'usuari</label>
								<input id="register_username" type="text" oninput="removeValidation(this.id);">
								<div id="register_username_validation" class="validation-message"></div>
								<label for="register_password">Contrasenya</label>
								<input id="register_password" type="password" oninput="removeValidation(this.id);">
								<div id="register_password_validation" class="validation-message"></div>
								<label for="register_repeat_password">Repeteix la contrasenya</label>
								<input id="register_repeat_password" type="password" oninput="removeValidation(this.id);">
								<div id="register_repeat_password_validation" class="validation-message"></div>
								<label for="register_email">Adreça electrònica</label>
								<input id="register_email" type="email" oninput="removeValidation(this.id);">
								<div id="register_email_validation" class="validation-message"></div>
								<label for="register_birthday_day">Data de naixement</label>
								<div class="date-chooser">
									<input class="date-day" id="register_birthday_day" type="text" maxlength="2" oninput="removeValidationOnlyText('register_birthday');" placeholder="Dia">
									<select class="date-month" id="register_birthday_month" onchange="removeValidationOnlyText('register_birthday');">
										<option value="" disabled selected>Mes</option>
										<option value="01">gener</option>
										<option value="02">febrer</option>
										<option value="03">març</option>
										<option value="04">abril</option>
										<option value="05">maig</option>
										<option value="06">juny</option>
										<option value="07">juliol</option>
										<option value="08">agost</option>
										<option value="09">setembre</option>
										<option value="10">octubre</option>
										<option value="11">novembre</option>
										<option value="12">desembre</option>
									</select>
									<input class="date-year" id="register_birthday_year" type="text" maxlength="4" oninput="removeValidationOnlyText('register_birthday');" placeholder="Any">
								</div>
								<div id="register_birthday_validation" class="validation-message"></div>
								<div class="checkbox-layout">
									<input id="register_privacy_policy_accept" type="checkbox" onchange="removeValidationOnlyText('register_privacy_policy_accept');">
									<label for="register_privacy_policy_accept">Accepto la <a href="<?php echo $main_url; ?>/politica-de-privadesa" target="_blank">política de privadesa</a></label>
								</div>
								<div id="register_privacy_policy_accept_validation" class="validation-message"></div>
								<div id="register_generic_validation" class="validation-message-generic"></div>
								<button id="register_submit" type="submit" class="login-button">Registra-m’hi i inicia la sessió</button>
							</form>
						</div>
						<div class="login-footer">Ja t’hi has registrat? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
				</div>
			</div>
<?php
} else {
	if ($style_type=='catalogue') {
?>
			<div data-nosnippet id="overlay" class="hidden">
				<a id="overlay-close" style="display: none;"><span class="fa fa-times"></span></a>
				<div id="overlay-content"></div>
			</div>
			<div data-nosnippet id="alert-overlay" class="hidden flex">
				<div id="alert-overlay-content">
					<h2 class="section-title" id="alert-title">S'ha produït un error</h2>
					<div id="alert-message">S'ha produït un error desconegut.</div>
					<div id="alert-buttonbar">
						<button id="alert-refresh-button" class="hidden">Actualitza</button>
						<button id="alert-ok-button">D'acord</button>
					</div>
				</div>
			</div>
<?php
	}
?>
			<div class="main-body">
				<div class="header">
<?php
	if ($style_type=='main') {
?>
					<a class="social-link twitter-link fab fa-fw fa-twitter" href="https://twitter.com/fansubscat" target="_blank" alt="Twitter de Fansubs.cat"></a>
					<a class="social-link mastodon-link fab fa-fw fa-mastodon" href="https://mastodont.cat/@fansubscat" target="_blank" alt="Mastodon de Fansubs.cat"></a>
					<a class="social-link telegram-link fab fa-fw fa-telegram" href="https://t.me/fansubscat" target="_blank" alt="Telegram de Fansubs.cat"></a>
<?php
	} else {
?>
					<a class="logo-small" href="<?php echo $main_url; ?>" title="Torna a la pàgina d’inici de Fansubs.cat">
						<?php include($static_directory.'/common/images/logo.svg'); ?>
<?php
		if ($style_type=='catalogue' && $is_hentai_site) {
?>
						<div class="catalogues-explicit-category">
							<span class="fa-stack" style="vertical-align: top;">
								<i class="fa-solid fa-fw fa-pepper-hot fa-stack-2x"></i>
								<i class="fa-solid fa-fw fa-plus fa-stack-1x"></i>
								<i class="fa-solid fa-fw fa-1 fa-stack-1x"></i>
								<i class="fa-solid fa-fw fa-8 fa-stack-1x"></i>
							</span>
						</div>
<?php
		}
?>
					</a>
<?php
		if ($style_type=='catalogue') {
?>
					<div class="catalogues-navigation">
						<a href="<?php echo $anime_url.($is_hentai_site ? '/hentai' : ''); ?>"<?php echo $cat_config['items_type']=='anime' ? ' class="catalogue-selected"' : ''; ?>>Anime</a>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo $manga_url.($is_hentai_site ? '/hentai' : ''); ?>"<?php echo $cat_config['items_type']=='manga' ? ' class="catalogue-selected"' : ''; ?>>Manga</a>
<?php
			if (!$is_hentai_site) {
?>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo $liveaction_url; ?>"<?php echo $cat_config['items_type']=='liveaction' ? ' class="catalogue-selected"' : ''; ?>>Acció real</a>
<?php
			}
?>
						<span class="catalogues-underline"></span>
					</div>
<?php
		}
	}
?>
					<div class="separator">
<?php
	if ($style_type=='catalogue' && empty($hide_hentai) && (is_adult() || is_robot())) {
		if (!$is_hentai_site) {
?>
						<a class="hentai-button" href="/hentai<?php echo !empty($is_searching) ? '/cerca' : ''; ?>" title="Vés a l'apartat de hentai">
							<span class="fa-stack" style="vertical-align: top;">
								<i class="fa-solid fa-fw fa-pepper-hot fa-stack-2x"></i>
								<i class="fa-solid fa-fw fa-plus fa-stack-1x"></i>
								<i class="fa-solid fa-fw fa-1 fa-stack-1x"></i>
								<i class="fa-solid fa-fw fa-8 fa-stack-1x"></i>
							</span>
						</a>
<?php
		} else {
?>
						<a class="hentai-button" href="<?php echo !empty($is_searching) ? '/cerca' : '/'; ?>" title="Vés al contingut general">
							<i class="fa-solid fa-fw fa-house-chimney fa-2x"></i>
						</a>
<?php
		}
	}
	if ($style_type=='catalogue' && empty($is_searching)) {
?>
						<a class="filter-button" href="<?php echo $is_hentai_site ? '/hentai' : ''; ?>/cerca" title="Filtra i mostra tot el catàleg">
							<span class="fa-stack" style="vertical-align: top;">
								<i class="fa-solid fa-fw fa-grip fa-stack-2x"></i>
								<i class="fa-solid fa-fw fa-filter fa-stack-1x"></i>
							</span>
						</a>
						<div class="search-form">
							<form id="search_form">
								<input id="search_query" type="text" value="<?php echo !empty($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Cerca..."<?php echo empty($header_series_page) ? ' autofocus' : ''; ?>>
								<i id="search_button" class="fa fa-search" title="Cerca en tot el catàleg"></i>
							</form>
						</div>
<?php
	} else if ($style_type=='news') {
?>
						<a class="groups-button" href="<?php echo $groups_url; ?>" title="Coneix els grups que subtitulen i editen">
							<span class="fa-stack" style="vertical-align: top;">
								<i class="fa-solid fa-fw fa-users fa-stack-2x"></i>
								<i class="fa-solid fa-fw fa-circle-info fa-stack-1x"></i>
							</span>
						</a>
						<div class="search-form">
							<form id="search_form">
								<input id="search_query" type="text" value="<?php echo !empty($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Cerca...">
								<i id="search_button" class="fa fa-search" title="Cerca a totes les notícies"></i>
							</form>
						</div>
<?php
	}
?>
					</div>
<?php
	if (empty($user)) {
?>
					<a class="user-login" href="<?php echo $users_url.'/inicia-la-sessio'; ?>"><span class="user-login-text">Inicia la sessió</span><span class="user-login-icon"><i class="fa fa-fw fa-sign-in"></i></span></a>
<?php
	}
?>
					<div class="user-options">
						<div class="dropdown-menu">
<?php
	if (!empty($user)) {
?>
							<img alt="Menú de l’usuari" onclick="showUserDropdown();" class="user-avatar dropdown-button" src="<?php echo !empty($user['avatar_filename']) ? $static_url.'/images/avatars/'.$user['avatar_filename'] : $static_url.'/common/images/noavatar.jpg'; ?>">
<?php
	} else {
?>
							<div onclick="showUserDropdown();" class="anon-avatar dropdown-button"><i class="fa fa-gear"></i></div>
<?php
	}
?>
							<div id="user-dropdown" class="dropdown-content">
								<div class="dropdown-title"><?php echo !empty($user) ? $user['username'] : 'Opcions'; ?></div>
								<hr class="dropdown-separator">
<?php
	if (!empty($user)) {
?>
								<a href="<?php echo $users_url; ?>"><i class="fa fa-fw fa-user"></i> El meu perfil</a>
								<a href="<?php echo $users_url.'/la-meva-llista'; ?>"><i class="fa fa-fw fa-list-ul"></i> La meva llista</a>
								<hr class="dropdown-separator-secondary">
<?php
	}
?>
								<a href="<?php echo $users_url.'/configuracio'; ?>"><i class="fa fa-fw fa-gear"></i> Configuració</a>
								<a class="theme-button" onclick="toggleSiteTheme();"><i class="fa fa-fw fa-circle-half-stroke"></i> <span class="theme-button-text"><?php echo (!empty($_COOKIE['site_theme']) && $_COOKIE['site_theme']=='light') ? 'Canvia al tema fosc' : 'Canvia al tema clar'; ?></span></a>
								<hr class="dropdown-separator-secondary">
<?php
	if (!empty($user)) {
?>
								<a href="<?php echo $users_url.'/tanca-la-sessio'; ?>"><i class="fa fa-fw fa-sign-out"></i> Tanca la sessió</a>
<?php
	} else {
?>
								<a href="<?php echo $users_url.'/inicia-la-sessio'; ?>"><i class="fa fa-fw fa-sign-in"></i> Inicia la sessió</a>
<?php
	}
?>
							</div>
						</div>
					</div>
				</div>
				<div class="main-section">
<?php
}
?>
