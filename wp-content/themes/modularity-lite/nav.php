<!-- Navigation -->
  <div id="nav">
    <ul>
      <li class="drop"><span>Categories</span>
        <ul>
			<?php wp_list_categories('orderby=name&depth=-1&title_li='); ?>
        </ul>
      </li>
      <li class="drop"><span>Pages</span>
        <ul>
        	<?php wp_list_pages('orderby=name&depth=-1&title_li='); ?>
        </ul>
      </li>
      <li class="drop"><span>Subscribe</span>
        <ul>
          <li><a href="<?php bloginfo('rss2_url'); ?>" class="icon entries">Subscribe to content</a></li>
          <li><a href="<?php bloginfo('comments_rss2_url'); ?>" class="icon comments">Subscribe to comments</a></li>
        </ul>
      </li>
      <li class="drop"><span>Contact</span>
        <ul>
          <li><a href="tel:<?php echo $phone; ?>" class="icon phone"><?php echo $phone; ?></a></li>
          <li><a href="mailto:<?php echo $email; ?>" class="icon email"><?php echo $email; ?></a></li>
        </ul>
      </li>
    </ul>
  </div>