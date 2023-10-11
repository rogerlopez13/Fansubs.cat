<?php
//This file sets config depending on the hostname used to display the site
//This allows customization but keeping the same codebase
$server_url = 'https://' . (!empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'unknown');

if ($server_url==HENTAI_URL) {
	$path = explode('/', $_SERVER['REQUEST_URI']);
	$server_url .= count($path)>1 ? '/'.$path[1] : '/';
	if ($server_url!=HENTAI_ANIME_URL && $server_url!=HENTAI_MANGA_URL) {
		//Used for the error page when not using a path
		$server_url=MAIN_URL;
	}
}
define('SITE_BASE_URL', $server_url);
switch ($server_url) {
	case NEWS_URL:
		define('SITE_TITLE', 'Notícies dels fansubs en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'news');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'news');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	case USERS_URL:
		define('SITE_TITLE', 'Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'users');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'users');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	case ANIME_URL:
		define('SITE_TITLE', 'Anime en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs veure en línia tot l’anime subtitulat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'anime');
		define('SITE_PREVIEW_IMAGE', 'anime');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', FALSE);
		define('CATALOGUE_ITEM_TYPE', 'anime');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-film');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-tv');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'anime');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming més de %d animes subtitulats en català. Ara pots gaudir de tot l’anime de tots els fansubs en català en un únic lloc.');
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera emissió');
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 50);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua mirant');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'temporades');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Capítols normals');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'Capítol únic');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Temporada');
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Animes amb temàtiques en comú');
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Altres continguts amb temàtiques en comú');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més temporades sense elements disponibles. Prem aquí per a mostrar-les totes.');
		break;
	case MANGA_URL:
		define('SITE_TITLE', 'Manga en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs llegir en línia tot el manga editat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'manga');
		define('SITE_PREVIEW_IMAGE', 'manga');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', FALSE);
		define('CATALOGUE_ITEM_TYPE', 'manga');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'manga');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet llegir en línia més de %d mangues editats en català. Ara pots gaudir de tot el manga de tots els fansubs en català en un únic lloc.');
		define('CATALOGUE_MINIMUM_DURATION', 1);
		define('CATALOGUE_MAXIMUM_DURATION', 100);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'pages');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera publicació');
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', TRUE);
		define('CATALOGUE_ROUND_INTERVAL', 50);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-book-open');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua llegint');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'volums');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Volum únic');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'One-shot');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Volum');
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Mangues amb temàtiques en comú');
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Altres continguts amb temàtiques en comú');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més volums sense elements disponibles. Prem aquí per a mostrar-los tots.');
		break;
	case LIVEACTION_URL:
		define('SITE_TITLE', 'Imatge real en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs veure en línia tot el contingut d’imatge real subtitulat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'liveaction');
		define('SITE_PREVIEW_IMAGE', 'liveaction');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', FALSE);
		define('CATALOGUE_ITEM_TYPE', 'liveaction');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-video');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-display');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'contingut d’imatge real');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming més de %d continguts d’imatge real subtitulats en català. Ara pots gaudir de tot el contingut d’imatge real de tots els fansubs en català en un únic lloc.');
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyDramaList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera emissió');
		define('CATALOGUE_HAS_DEMOGRAPHIES', FALSE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 25);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua mirant');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'temporades');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Capítols normals');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'Capítol únic');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Temporada');
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Continguts d’imatge real amb temàtiques en comú');
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Altres continguts amb temàtiques en comú');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més temporades sense elements disponibles. Prem aquí per a mostrar-les totes.');
		break;
	case HENTAI_ANIME_URL:
		define('SITE_TITLE', 'Anime hentai en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs veure en línia tot l’anime hentai subtitulat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'hentai');
		define('SITE_PREVIEW_IMAGE', 'hentai_anime');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', TRUE);
		define('CATALOGUE_ITEM_TYPE', 'anime');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'movie');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'series');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-video');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-display');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'Films');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Sèries');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'anime');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet veure en streaming anime hentai subtitulat en català. Ara pots gaudir de tot l’anime hentai de tots els fansubs en català en un únic lloc.');
		define('CATALOGUE_MINIMUM_DURATION', 0);
		define('CATALOGUE_MAXIMUM_DURATION', 120);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'time');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera emissió');
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', FALSE);
		define('CATALOGUE_ROUND_INTERVAL', 25);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-play');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua mirant');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'temporades');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Capítols normals');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'Capítol únic');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Temporada');
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Animes hentai amb temàtiques en comú');
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Mangues hentai amb temàtiques en comú');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més temporades sense elements disponibles. Prem aquí per a mostrar-les totes.');
		break;
	case HENTAI_MANGA_URL:
		define('SITE_TITLE', 'Manga hentai en català | Fansubs.cat');
		define('SITE_DESCRIPTION', 'Aquí podràs llegir en línia tot el manga hentai editat pels fansubs en català!');
		define('SITE_INTERNAL_NAME', 'hentai');
		define('SITE_PREVIEW_IMAGE', 'hentai_manga');
		define('SITE_INTERNAL_TYPE', 'catalogue');
		define('SITE_IS_CATALOGUE', TRUE);
		define('SITE_IS_HENTAI', TRUE);
		define('CATALOGUE_ITEM_TYPE', 'manga');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID', 'oneshot');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID', 'serialized');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON', 'fa-book-open');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON', 'fa-book');
		define('CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME', 'One-shots');
		define('CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME', 'Serialitzats');
		define('CATALOGUE_ITEM_STRING_SINGULAR', 'manga');
		define('CATALOGUE_ROBOT_MESSAGE', 'Fansubs.cat et permet llegir en línia manga hentai en català. Ara pots gaudir de tot el manga hentai de tots els fansubs en català en un únic lloc.');
		define('CATALOGUE_MINIMUM_DURATION', 1);
		define('CATALOGUE_MAXIMUM_DURATION', 100);
		define('CATALOGUE_DURATION_SLIDER_FORMATTING', 'pages');
		define('CATALOGUE_SCORE_SOURCE', 'MyAnimeList');
		define('CATALOGUE_FIRST_PUBLISH_STRING', 'Any de primera publicació');
		define('CATALOGUE_HAS_DEMOGRAPHIES', TRUE);
		define('CATALOGUE_HAS_ORIGIN', TRUE);
		define('CATALOGUE_ROUND_INTERVAL', 25);
		define('CATALOGUE_PLAY_BUTTON_ICON', 'fa-book-open');
		define('CATALOGUE_CONTINUE_WATCHING_STRING', 'Continua llegint');
		define('CATALOGUE_SEASON_STRING_PLURAL', 'volums');
		define('CATALOGUE_SEASON_STRING_UNIQUE', 'Volum únic');
		define('CATALOGUE_SEASON_STRING_UNIQUE_SINGLE', 'One-shot');
		define('CATALOGUE_SEASON_STRING_SINGULAR_CAPS', 'Volum');
		define('CATALOGUE_RECOMMENDATION_STRING_SAME_TYPE', 'Mangues hentai amb temàtiques en comú');
		define('CATALOGUE_RECOMMENDATION_STRING_DIFFERENT_TYPE', 'Animes hentai amb temàtiques en comú');
		define('CATALOGUE_MORE_SEASONS_AVAILABLE', 'Hi ha més volums sense elements disponibles. Prem aquí per a mostrar-los tots.');
		break;
	case HENTAI_URL:
		define('SITE_TITLE', 'Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'main');
		define('SITE_INTERNAL_TYPE', 'main');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	case MAIN_URL:
		define('SITE_TITLE', 'Fansubs.cat');
		define('SITE_DESCRIPTION', 'A Fansubs.cat trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.');
		define('SITE_INTERNAL_NAME', 'main');
		define('SITE_PREVIEW_IMAGE', 'main');
		define('SITE_INTERNAL_TYPE', 'main');
		define('SITE_IS_CATALOGUE', FALSE);
		define('SITE_IS_HENTAI', FALSE);
		break;
	default:
		//Nothing to define for now
		break;
}
?>
