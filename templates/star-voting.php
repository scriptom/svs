<ul class="stars">
	<?php for ( $i = 1; $i <= SVS_MAX_SCORE; $i++ ): ?>
    <li><?= $i ?></li>
   <?php endfor; ?>
    <div class="stat" style="width:<?= $puntaje ?>%"><?php for ( $i = 1; $i <= SVS_MAX_SCORE; $i++ ): ?><span></span><?php endfor; ?></div>
    <div class="voto">
    	<?php for ( $i = SVS_MAX_SCORE; $i >= 1; $i-- ): ?>
    	<span><a href="#" data-value="<?= $i ?>"><?= $i ?></a></span>
	   <?php endfor; ?>
    </div>
</ul>
