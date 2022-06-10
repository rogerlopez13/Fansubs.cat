<?php
//This file sets config depending on the hostname used to display the catalogue
//This allows customization but keeping the same codebase
switch ($_SERVER['HTTP_HOST']) {
	case 'manga.fansubs.cat':
	case 'mangav2.fansubs.cat':
		$config = array(
			'base_url' => "https://manga.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/social/manga.jpg",
			'site_title' => "Fansubs.cat - Manga en català",
			'site_description' => "Aquí podràs veure en línia tot el manga editat pels fansubs en català!",
			'site_robot_message' => "Fansubs.cat et permet llegir en línia més de %d mangues editats en català. Ara pots gaudir de tot el manga de tots els fansubs en català en un únic lloc.",
			'items_type' => "manga",
			'filmsoneshots' => "One-shots",
			'filmsoneshots_icon' => "fa-book-open",
			'filmsoneshots_s' => "One-shot",
			'serialized' => "Serialitzats",
			'serialized_icon' => "fa-book",
			'filmsoneshots_slug' => "one-shots",
			'serialized_slug' => "serialitzats",
			'filmsoneshots_slug_internal' => "oneshots",
			'serialized_slug_internal' => "serialized",
			'filmsoneshots_db' => "oneshot",
			'serialized_db' => "serialized",
			'filmsoneshots_tadaima_forum_id' => "9",
			'serialized_tadaima_forum_id' => "9",
			'items_string_s' => "manga",
			'items_string_p' => "mangues",
			'items_string_del' => "del manga",
			'being_published' => "en publicació",
			'more_divisions_available' => "Hi ha més volums sense contingut disponible. Prem per a mostrar-los tots.",
			'division_name' => "Volum",
			'division_name_lc' => "volum",
			'preview_prefix' => "Manga",
			//Sections
			'section_moviesoneshots' => "Catàleg de one-shots",
			'section_moviesoneshots_desc' => "Tria i remena entre un catàleg de %d mangues de curta durada!",
			'section_serialized' => "Catàleg de mangues serialitzats",
			'section_serialized_desc' => "Tria i remena entre un catàleg de %d mangues serialitzats!",
			'section_advent' => "<span class=\"iconsm fa fa-fw fa-gift\"></span> Calendari d'advent",
			'section_advent_desc' => "Enguany, tots els fansubs en català s'uneixen per a dur-vos cada dia una novetat! Bones festes!",
			'section_featured' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Mangues destacats",
			'section_featured_desc' => "Aquí tens la tria de mangues recomanats d'aquesta setmana! T'animes a llegir-ne algun?",
			'section_last_updated' => "<span class=\"iconsm fa fa-fw fa-clock\"></span> Darreres actualitzacions",
			'section_last_updated_desc' => "Aquestes són les darreres novetats de manga editat en català pels diferents fansubs.",
			'section_last_completed' => "<span class=\"iconsm fa fa-fw fa-check\"></span> Finalitzats recentment",
			'section_last_completed_desc' => "Vet aquí els darrers mangues completats. Si els mires, no et caldrà esperar-ne nous capítols!",
			'section_random' => "<span class=\"iconsm fa fa-fw fa-dice\"></span> A l'atzar",
			'section_random_desc' => "T'agrada provar sort? Aquí tens un seguit de mangues triats a l'atzar. Si no te'n convenç cap, actualitza la pàgina i torna-hi!",
			'section_popular' => "<span class=\"iconsm fa fa-fw fa-fire\"></span> Més populars",
			'section_popular_desc' => "Aquests són els mangues que més han vist els nostres usuaris durant la darrera quinzena.",
			'section_more_recent' => "<span class=\"iconsm fa fa-fw fa-stopwatch\"></span> Més actuals",
			'section_more_recent_desc' => "T'agrada el manga d'actualitat? Aquests són els mangues més nous que tenim editats.",
			'section_best_rated' => "<span class=\"iconsm fa fa-fw fa-heart\"></span> Més ben valorats",
			'section_best_rated_desc' => "Els mangues més ben puntuats pels usuaris de MyAnimeList amb versió editada en català.",
			'section_fools' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Obres mestres",
			'section_fools_desc' => "Que no t'enganyin, aquests són els millors mangues de la història. Si encara no els has llegit, què esperes?",
			'section_sant_jordi' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Especial Sant Jordi",
			'section_sant_jordi_desc' => "Mangues ben valorats amb components romàntics. T'animes a llegir-ne algun?",
			'section_search_results' => "Resultats de la cerca",
			'section_search_results_desc' => "La cerca de \"%s\" ha obtingut %s resultats a la nostra base de dades de manga.",
			'section_search_other_results' => "Altres resultats",
			'section_search_other_results_desc_s' => "Hem trobat %d altre contingut que coincideix amb la cerca.",
			'section_search_other_results_desc_p' => "Hem trobat %d altres continguts que coincideixen amb la cerca.",
			'section_related' => "<span class=\"iconsm fa fa-fw fa-book-open\"></span> Mangues recomanats",
			'section_related_desc' => "Si t'agrada aquest manga, és possible que també t'agradin els d'aquesta llista:",
			'section_related_other' => "<span class=\"iconsm fa fa-fw fa-tv\"></span> Altres continguts recomanats",
			'section_related_other_desc' => "Si t'agrada aquest manga, és possible que també t'agradin aquests altres continguts:",
			'view_now' => "Llegeix-lo ara",
			'option_show_cancelled' => "Mostra els mangues cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els mangues amb algun capítol sense enllaç vàlid",
		);
		break;
	case 'accioreal.fansubs.cat':
	case 'acciorealv2.fansubs.cat':
		$config = array(
			'base_url' => "https://accioreal.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/social/liveaction.jpg",
			'site_title' => "Fansubs.cat - Acció real en català",
			'site_description' => "Aquí podràs veure en línia tot el contingut d'acció real subtitulat pels fansubs en català!",
			'site_robot_message' => "Fansubs.cat et permet veure en streaming més de %d continguts d'acció real subtitulats en català. Ara pots gaudir de tot el contingut d'acció real de tots els fansubs en català en un únic lloc.",
			'items_type' => "liveaction",
			'filmsoneshots' => "Films",
			'filmsoneshots_icon' => "fa-video",
			'filmsoneshots_s' => "Film",
			'serialized' => "Sèries",
			'serialized_icon' => "fa-tv",
			'filmsoneshots_slug' => "films",
			'serialized_slug' => "series",
			'filmsoneshots_slug_internal' => "movies",
			'serialized_slug_internal' => "series",
			'filmsoneshots_db' => "movie",
			'serialized_db' => "series",
			'filmsoneshots_tadaima_forum_id' => "14",
			'serialized_tadaima_forum_id' => "16",
			'items_string_s' => "contingut",
			'items_string_p' => "continguts",
			'items_string_del' => "del contingut",
			'being_published' => "en emissió",
			'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
			'division_name' => "Temporada",
			'division_name_lc' => "temporada",
			'preview_prefix' => "Acció real",
			//Sections
			'section_moviesoneshots' => "Catàleg de films",
			'section_moviesoneshots_desc' => "Tria i remena entre un catàleg de %d films! Prepara les crispetes!",
			'section_serialized' => "Catàleg de sèries",
			'section_serialized_desc' => "Tria i remena entre un catàleg de %d sèries! Compte, que enganxen!",
			'section_advent' => "<span class=\"iconsm fa fa-fw fa-gift\"></span> Calendari d'advent",
			'section_advent_desc' => "Enguany, tots els fansubs en català s'uneixen per a dur-vos cada dia una novetat! Bones festes!",
			'section_featured' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Continguts d'acció real destacats",
			'section_featured_desc' => "Aquí tens la tria de continguts d'acció real recomanats d'aquesta setmana! T'animes a mirar-ne algun?",
			'section_last_updated' => "<span class=\"iconsm fa fa-fw fa-clock\"></span> Darreres actualitzacions",
			'section_last_updated_desc' => "Aquestes són les darreres novetats d'acció real versionades en català pels diferents fansubs.",
			'section_last_completed' => "<span class=\"iconsm fa fa-fw fa-check\"></span> Finalitzats recentment",
			'section_last_completed_desc' => "Vet aquí els darrers continguts completats. Si els mires, no et caldrà esperar-ne nous capítols!",
			'section_random' => "<span class=\"iconsm fa fa-fw fa-dice\"></span> A l'atzar",
			'section_random_desc' => "T'agrada provar sort? Aquí tens un seguit de continguts d'acció real triats a l'atzar. Si no te'n convenç cap, actualitza la pàgina i torna-hi!",
			'section_popular' => "<span class=\"iconsm fa fa-fw fa-fire\"></span> Més populars",
			'section_popular_desc' => "Aquests són els continguts d'acció real que més han vist els nostres usuaris durant la darrera quinzena.",
			'section_more_recent' => "<span class=\"iconsm fa fa-fw fa-stopwatch\"></span> Més actuals",
			'section_more_recent_desc' => "T'agrada el contingut d'actualitat? Aquests són els continguts d'acció real més nous que tenim editats en català.",
			'section_best_rated' => "<span class=\"iconsm fa fa-fw fa-heart\"></span> Més ben valorats",
			'section_best_rated_desc' => "Els continguts més ben puntuats pels usuaris de MyDramaList amb alguna versió feta en català.",
			'section_fools' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Obres mestres",
			'section_fools_desc' => "Que no t'enganyin, aquests són els millors continguts d'acció real de la història. Si encara no els has vist, què esperes?",
			'section_sant_jordi' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Especial Sant Jordi",
			'section_sant_jordi_desc' => "Continguts d'acció real ben valorats amb components romàntics. T'animes a mirar-ne algun?",
			'section_search_results' => "Resultats de la cerca",
			'section_search_results_desc' => "La cerca de \"%s\" ha obtingut %s resultats a la nostra base de dades d'acció real.",
			'section_search_other_results' => "Altres resultats",
			'section_search_other_results_desc_s' => "Hem trobat %d altre contingut que coincideix amb la cerca.",
			'section_search_other_results_desc_p' => "Hem trobat %d altres continguts que coincideixen amb la cerca.",
			'section_related' => "<span class=\"iconsm fa fa-fw fa-tv\"></span> Animes recomanats",
			'section_related_desc' => "Si t'agrada aquest contingut d'acció real, és possible que també t'agradin els d'aquesta llista:",
			'section_related_other' => "<span class=\"iconsm fa fa-fw fa-book-open\"></span> Altres continguts recomanats",
			'section_related_other_desc' => "Si t'agrada aquest contingut d'acció real, és possible que també t'agradin aquests altres continguts:",
			'view_now' => "Mira'l ara",
			'option_show_cancelled' => "Mostra els continguts d'acció real cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els continguts d'acció real amb algun capítol sense enllaç vàlid",
		);
		break;
	case 'anime.fansubs.cat':
	case 'animev2.fansubs.cat':
	default:
		$config = array(
			'base_url' => "https://anime.fansubs.cat",
			'preview_image' => "https://static.fansubs.cat/social/anime.jpg",
			'site_title' => "Fansubs.cat - Anime en català",
			'site_description' => "Aquí podràs veure en línia tot l'anime subtitulat pels fansubs en català!",
			'site_robot_message' => "Fansubs.cat et permet veure en streaming més de %d animes subtitulats en català. Ara pots gaudir de tot l'anime de tots els fansubs en català en un únic lloc.",
			'items_type' => "anime",
			'filmsoneshots' => "Films",
			'filmsoneshots_icon' => "fa-video",
			'filmsoneshots_s' => "Film",
			'serialized' => "Sèries",
			'serialized_icon' => "fa-tv",
			'filmsoneshots_slug' => "films",
			'serialized_slug' => "series",
			'filmsoneshots_slug_internal' => "movies",
			'serialized_slug_internal' => "series",
			'filmsoneshots_db' => "movie",
			'serialized_db' => "series",
			'filmsoneshots_tadaima_forum_id' => "14",
			'serialized_tadaima_forum_id' => "10",
			'items_string_s' => "anime",
			'items_string_p' => "animes",
			'items_string_del' => "de l'anime",
			'being_published' => "en emissió",
			'more_divisions_available' => "Hi ha més temporades sense contingut disponible. Prem per a mostrar-les totes.",
			'division_name' => "Temporada",
			'division_name_lc' => "temporada",
			'preview_prefix' => "Anime",
			//Sections
			'section_moviesoneshots' => "Catàleg de films",
			'section_moviesoneshots_desc' => "Tria i remena entre un catàleg de %d films! Prepara les crispetes!",
			'section_serialized' => "Catàleg de sèries",
			'section_serialized_desc' => "Tria i remena entre un catàleg de %d sèries! Compte, que enganxen!",
			'section_advent' => "<span class=\"iconsm fa fa-fw fa-gift\"></span> Calendari d'advent",
			'section_advent_desc' => "Enguany, tots els fansubs en català s'uneixen per a dur-vos cada dia una novetat! Bones festes!",
			'section_featured' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Animes destacats",
			'section_featured_desc' => "Aquí tens la tria d'animes recomanats d'aquesta setmana! T'animes a mirar-ne algun?",
			'section_last_updated' => "<span class=\"iconsm fa fa-fw fa-clock\"></span> Darreres actualitzacions",
			'section_last_updated_desc' => "Aquestes són les darreres novetats d'anime versionat en català pels diferents fansubs.",
			'section_last_completed' => "<span class=\"iconsm fa fa-fw fa-check\"></span> Finalitzats recentment",
			'section_last_completed_desc' => "Vet aquí els darrers animes completats. Si els mires, no et caldrà esperar-ne nous capítols!",
			'section_random' => "<span class=\"iconsm fa fa-fw fa-dice\"></span> A l'atzar",
			'section_random_desc' => "T'agrada provar sort? Aquí tens un seguit d'animes triats a l'atzar. Si no te'n convenç cap, actualitza la pàgina i torna-hi!",
			'section_popular' => "<span class=\"iconsm fa fa-fw fa-fire\"></span> Més populars",
			'section_popular_desc' => "Aquests són els animes que més han vist els nostres usuaris durant la darrera quinzena.",
			'section_more_recent' => "<span class=\"iconsm fa fa-fw fa-stopwatch\"></span> Més actuals",
			'section_more_recent_desc' => "T'agrada l'anime d'actualitat? Aquests són els animes més nous que tenim editats en català.",
			'section_best_rated' => "<span class=\"iconsm fa fa-fw fa-heart\"></span> Més ben valorats",
			'section_best_rated_desc' => "Els animes més ben puntuats pels usuaris de MyAnimeList amb alguna versió feta en català.",
			'section_fools' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Obres mestres",
			'section_fools_desc' => "Que no t'enganyin, aquests són els millors animes de la història. Si encara no els has vist, què esperes?",
			'section_sant_jordi' => "<span class=\"iconsm fa fa-fw fa-star\"></span> Especial Sant Jordi",
			'section_sant_jordi_desc' => "Animes ben valorats amb components romàntics. T'animes a mirar-ne algun?",
			'section_search_results' => "Resultats de la cerca",
			'section_search_results_desc' => "La cerca de \"%s\" ha obtingut %s resultats a la nostra base de dades d'anime.",
			'section_search_other_results' => "Altres resultats",
			'section_search_other_results_desc_s' => "Hem trobat %d altre contingut que coincideix amb la cerca.",
			'section_search_other_results_desc_p' => "Hem trobat %d altres continguts que coincideixen amb la cerca.",
			'section_related' => "<span class=\"iconsm fa fa-fw fa-tv\"></span> Animes recomanats",
			'section_related_desc' => "Si t'agrada aquest anime, és possible que també t'agradin els d'aquesta llista:",
			'section_related_other' => "<span class=\"iconsm fa fa-fw fa-book-open\"></span> Altres continguts recomanats",
			'section_related_other_desc' => "Si t'agrada aquest anime, és possible que també t'agradin aquests altres continguts:",
			'view_now' => "Mira'l ara",
			'option_show_cancelled' => "Mostra els animes cancel·lats o abandonats pels fansubs",
			'option_show_missing' => "Mostra els animes amb algun capítol sense enllaç vàlid",
		);
		break;
}
?>
