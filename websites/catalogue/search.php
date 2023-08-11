<?php
define('PAGE_STYLE_TYPE', 'catalogue');
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

validate_hentai();

define('PAGE_TITLE', 'Resultats de la cerca');

if (is_robot()) {
	define('PAGE_EXTRA_BODY_CLASS', 'has-search-results');
}

$_GET['query']=str_replace('%2F', '/', isset($_GET['query']) ? $_GET['query'] : '');

define('PAGE_PATH', SITE_PATH.'/cerca'.(isset($_GET['query']) ? '/'.urlencode($_GET['query']) : ''));
define('PAGE_IS_SEARCH', TRUE);
if (!is_robot()) {
	define('SKIP_FOOTER', TRUE);
}

require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="search-layout">
						<input class="search-base-url" type="hidden" value="<?php echo SITE_PATH.'/cerca'; ?>">
						<div class="search-filter-title">Filtres del catàleg</div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="catalogue-search-query">Text a cercar</label>
							<input id="catalogue-search-query" type="text" oninput="loadSearchResults();" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Cerca...">
							<label>Tipus</label>
							<div id="catalogue-search-type" class="singlechoice-selector singlechoice-type">
								<div class="singlechoice-button singlechoice-selected" onclick="singlechoiceChange(this);" data-value="all"><i class="fa fa-fw fa-grip"></i>Tots</div>
								<div class="singlechoice-button" onclick="singlechoiceChange(this);" data-value="<?php echo CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID; ?>"><i class="fa fa-fw <?php echo CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON; ?>"></i><?php echo CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME; ?></div>
								<div class="singlechoice-button" onclick="singlechoiceChange(this);" data-value="<?php echo CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID; ?>"><i class="fa fa-fw <?php echo CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON; ?>"></i><?php echo CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME; ?></div>
							</div>
							<label>Estat</label>
<?php
$statuses=array(1,3,2,4,5);
foreach ($statuses as $status_id) {
?>
							<div class="search-checkboxes search-status status-<?php echo get_status($status_id); ?>">
								<input id="catalogue-search-status-<?php echo $status_id; ?>" data-id="<?php echo $status_id; ?>" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-status-<?php echo $status_id; ?>" class="for-checkbox"><span class="status-indicator"></span> <?php echo get_status_description_short($status_id); ?></label>
							</div>
<?php
}
?>
							<label>Durada mitjana</label>
							<div id="catalogue-search-duration" class="double-slider-container">
								<input id="duration-from-slider" class="double-slider-from" type="range" value="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? '1' : '0'; ?>" min="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? '1' : '0'; ?>" max="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? '100' : '120'; ?>" onchange="loadSearchResults();">
								<input id="duration-to-slider" class="double-slider-to" type="range" value="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? '100' : '120'; ?>" min="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? '1' : '0'; ?>" max="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? '100' : '120'; ?>" onchange="loadSearchResults();">
								<div id="duration-from-input" data-value-formatting="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? 'pages' : 'time'; ?>" class="double-slider-input-from"><?php echo CATALOGUE_ITEM_TYPE=='manga' ? '1 pàg.' : '0:00:00'; ?></div>
								<div id="duration-to-input" data-value-formatting="<?php echo CATALOGUE_ITEM_TYPE=='manga' ? 'pages' : 'time'; ?>-max" class="double-slider-input-to"><?php echo CATALOGUE_ITEM_TYPE=='manga' ? '100+ pàg.' : '2:00:00+'; ?></div>
							</div>
<?php
if (!SITE_IS_HENTAI) {
?>
							<label>Valoració per edats</label>
							<div id="catalogue-search-rating" class="double-slider-container">
								<input id="rating-from-slider" class="double-slider-from" type="range" value="0" min="0" max="4" onchange="loadSearchResults();">
								<input id="rating-to-slider" class="double-slider-to" type="range" value="4" min="0" max="4" onchange="loadSearchResults();">
								<div id="rating-from-input" data-value-formatting="rating" class="double-slider-input-from">TP</div>
								<div id="rating-to-input" data-value-formatting="rating" class="double-slider-input-to">+18</div>
							</div>
<?php
}
?>
							<label>Puntuació a <?php echo CATALOGUE_ITEM_TYPE=='liveaction' ? 'MyDramaList' : 'MyAnimeList'; ?></label>
							<div id="catalogue-search-score" class="double-slider-container">
								<input id="score-from-slider" class="double-slider-from" type="range" value="0" min="0" max="100" onchange="loadSearchResults();">
								<input id="score-to-slider" class="double-slider-to" type="range" value="100" min="0" max="100" onchange="loadSearchResults();">
								<div id="score-from-input" data-value-formatting="score" class="double-slider-input-from">-</div>
								<div id="score-to-input" data-value-formatting="score" class="double-slider-input-to">10,0</div>
							</div>
							<label>Any de primera <?php echo CATALOGUE_ITEM_TYPE=='manga' ? 'publicació' : 'emissió'; ?></label>
							<div id="catalogue-search-year" class="double-slider-container">
								<input id="year-from-slider" class="double-slider-from" type="range" value="1950" min="1950" max="<?php echo date('Y'); ?>" onchange="loadSearchResults();">
								<input id="year-to-slider" class="double-slider-to" type="range" value="<?php echo date('Y'); ?>" min="1950" max="<?php echo date('Y'); ?>" onchange="loadSearchResults();">
								<div id="year-from-input" data-value-formatting="year" class="double-slider-input-from">-</div>
								<div id="year-to-input" data-value-formatting="year" class="double-slider-input-to"><?php echo date('Y'); ?></div>
							</div>
							<label for="catalogue-search-fansub">Fansub</label>
							<select id="catalogue-search-fansub" onchange="loadSearchResults();">
<?php
if ((!empty($user) && count($user['blacklisted_fansub_ids'])>0) || (empty($user) && count(get_cookie_blacklisted_fansub_ids())>0)) {
?>
								<option value="-1">Tots (fins i tot llista negra)</option>
								<option value="-2">Tots (excepte llista negra)</option>
<?php
} else {
?>
								<option value="-1">Tots els fansubs</option>
<?php
}
$result = query_all_fansubs_with_versions($user);
while ($row = mysqli_fetch_assoc($result)) {
?>
								<option value="<?php echo $row['slug']; ?>"<?php echo (!empty($_GET['fansub']) && $_GET['fansub']==$row['slug']) ? ' selected' : ''; ?>><?php echo $row['name']; ?></option>
<?php
}
?>
							</select>
							<label>Inclou-hi també...</label>
							<div class="search-checkboxes">
								<input id="catalogue-search-include-lost" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-include-lost" class="for-checkbox">Fitxes amb capítols perduts</label>
							</div>
							<div class="search-checkboxes">
								<input id="catalogue-search-include-full-catalogue" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-include-full-catalogue" class="for-checkbox">Altres resultats de la cerca</label>
							</div>
<?php
if (CATALOGUE_ITEM_TYPE!='liveaction' && !SITE_IS_HENTAI) {
?>
							<label>Demografies</label>
<?php
	$result=query_filter_demographics();
	while ($row=mysqli_fetch_assoc($result)) {
?>
							<div class="search-checkboxes search-demographics">
								<input id="catalogue-search-demographics-<?php echo $row['id']; ?>" data-id="<?php echo $row['id']; ?>" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-demographics-<?php echo $row['id']; ?>" class="for-checkbox"><?php echo $row['name']; ?></label>
							</div>
<?php
	}
	mysqli_free_result($result);
?>
							<div class="search-checkboxes search-demographics">
								<input id="catalogue-search-demographics-not-set" data-id="-1" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-demographics-not-set" class="for-checkbox">No definida</label>
							</div>
<?php
}
?>
							<label>Gèneres</label>
<?php
$result=query_filter_genders();
while ($row=mysqli_fetch_assoc($result)) {
?>
							
							<div class="tristate-selector tristate-genres" data-id="<?php echo $row['id']; ?>">
								<div class="tristate-button tristate-include" onclick="tristateChange(this);"><i class="fa fa-fw fa-check"></i></div>
								<div class="tristate-button tristate-exclude" onclick="tristateChange(this);"><i class="fa fa-fw fa-xmark"></i></div>
								<div class="tristate-description"><?php echo htmlspecialchars($row['name']); ?></div>
							</div>
<?php
}
mysqli_free_result($result);
?>
							<label>Temàtiques</label>
<?php
$result=query_filter_themes();
while ($row=mysqli_fetch_assoc($result)) {
?>
							
							<div class="tristate-selector tristate-genres" data-id="<?php echo $row['id']; ?>">
								<div class="tristate-button tristate-include" onclick="tristateChange(this);"><i class="fa fa-fw fa-check"></i></div>
								<div class="tristate-button tristate-exclude" onclick="tristateChange(this);"><i class="fa fa-fw fa-xmark"></i></div>
								<div class="tristate-description"><?php echo htmlspecialchars($row['name']); ?></div>
							</div>
<?php
}
mysqli_free_result($result);
?>
						</form>
					</div>
					<div class="search-layout-toggle-button" onclick="toggleSearchLayout();"><i class="fa fa-fw fa-chevron-right"></i></div>
					<div class="results-layout catalogue-search<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	define('ROBOT_INCLUDED', TRUE);
	if (!empty($_GET['fansub'])) {
		$_POST['fansub']=$_GET['fansub'];
	}
	include("results.php");
	define('SKIP_FOOTER', TRUE);
}
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message">S’està carregant el catàleg...</div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message">S’ha produït un error en contactar amb el servidor. Torna-ho a provar.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>
