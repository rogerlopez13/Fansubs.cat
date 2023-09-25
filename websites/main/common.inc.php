<?php
function print_fansub($row, $is_hentai) {
?>
								<div class="fansub<?php echo !empty($row['is_blacklisted']) ? ' fansub-blacklisted' : ''; ?><?php echo $is_hentai ? ' subtheme-hentai' : ''; ?>">
									<div class="fansub-text-wrapper">
										<img class="fansub-icon" src="<?php echo STATIC_URL.'/images/icons/'.$row['id'].'.png'; ?>" alt="">
										<div class="fansub-info">
											<h3 class="fansub-name"><?php echo $row['name']; ?><?php echo !empty($row['type']=='fandub') ? ' <span class="fa fa-fw fa-microphone-lines" title="És un fandub: fa doblatges."></span>' : ''; ?><?php echo !empty($row['is_blacklisted']) ? ' <span class="fa fa-fw fa-ban" title="És a la teva llista negra. Pots eliminar-l’en a la configuració d’usuari."></span>' : ''; ?></h3>
<?php
	if (!empty($row['url']) && empty($row['archive_url'])) {
		$url = $row['url'];
	} else if (!empty($row['archive_url'])) {
		$url = $row['archive_url'];
	} else {
		$url = NULL;
	}
?>
											<div class="fansub-links">
<?php
	if ($url!=NULL){
?>
												<a class="fa fa-fw fa-earth-europe web-link" href="<?php echo $url; ?>" target="_blank"></a>
<?php
	}
?>
<?php
	if ($row['twitter_url']!=NULL){
?>
												<a class="fab fa-fw fa-twitter twitter-link" href="<?php echo $row['twitter_url']; ?>" target="_blank"></a>
<?php
	}
?>
<?php
	if ($row['mastodon_url']!=NULL){
?>
												<a class="fab fa-fw fa-mastodon mastodon-link" href="<?php echo $row['mastodon_url']; ?>" target="_blank"></a>
<?php
	}
?>
<?php
	if ($row['discord_url']!=NULL){
?>
												<a class="fab fa-fw fa-discord discord-link" href="<?php echo $row['discord_url']; ?>" target="_blank"></a>
<?php
	}
?>
											</div>
										</div>
									</div>
									<div class="fansub-content">
<?php
	if ($row['total_anime']>0 && empty($row['is_blacklisted'])) {
?>
												<a class="normal-button content-button" href="<?php echo ($is_hentai ? HENTAI_ANIME_URL : ANIME_URL).'/cerca?fansub='.urlencode($row['slug']); ?>"><span class="content-type">Animes</span><span class="content-quantity"><?php echo $row['total_anime']; ?></span></a>
<?php
	} else {
?>
												<span class="content-button disabled-content-button"><span class="content-type">Animes</span><span class="content-quantity"><?php echo $row['total_anime']; ?></span></span>
<?php
	}
	if ($row['total_manga']>0 && empty($row['is_blacklisted'])) {
?>
												<a class="normal-button content-button" href="<?php echo ($is_hentai ? HENTAI_MANGA_URL : MANGA_URL).'/cerca?fansub='.urlencode($row['slug']); ?>"><span class="content-type">Mangues</span><span class="content-quantity"><?php echo $row['total_manga']; ?></span></a>
<?php
	} else {
?>
												<span class="content-button disabled-content-button"><span class="content-type">Mangues</span><span class="content-quantity"><?php echo $row['total_manga']; ?></span></span>
<?php
	}
	if ($row['total_liveaction']>0 && empty($row['is_blacklisted'])) {
?>
												<a class="normal-button content-button" href="<?php echo ($is_hentai ? LIVEACTION_URL : LIVEACTION_URL).'/cerca?fansub='.urlencode($row['slug']); ?>"><span class="content-type">Imatge real</span><span class="content-quantity"><?php echo $row['total_liveaction']; ?></span></a>
<?php
	} else if (!$is_hentai) {
?>
												<span class="content-button disabled-content-button"><span class="content-type">Imatge real</span><span class="content-quantity"><?php echo $row['total_liveaction']; ?></span></span>
<?php
	}
	if ($row['total_news']>0 && empty($row['is_blacklisted'])) {
?>
												<a class="normal-button content-button" href="<?php echo NEWS_URL.'/cerca?fansub='.urlencode($row['slug']); ?>"><span class="content-type">Notícies</span><span class="content-quantity"><?php echo $row['total_news']; ?></span></a>
<?php
	} else {
?>
												<span class="content-button disabled-content-button"><span class="content-type">Notícies</span><span class="content-quantity"><?php echo $row['total_news']; ?></span></span>
<?php
	}
?>
									</div>
								</div>
<?php
}
function print_community($row) {
?>
							<a class="community-item" href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank">
								<img class="community-icon" src="<?php echo STATIC_URL.'/images/communities/'.$row['id'].'.png'; ?>" alt="" />
								<div class="community-data">
									<div class="community-title"><?php echo htmlspecialchars($row['name']); ?></div>
									<div class="community-description"><?php echo htmlspecialchars($row['description']); ?></div>
								</div>
							</a>
<?php
}
?>
