<?php
/**
 * @var tiFy\Contracts\Form\FormView $this
 * @var tiFy\Template\Templates\ListTable\Contracts\Item $item
 */
?>
<ul>
    <li>
        <label><?php _e('Session', 'tify'); ?> :</label>
        <span><?php echo $item->get('session'); ?></span>
    </li>
    <li>
        <label><?php _e('Date', 'tify'); ?> :</label>
        <span><?php echo $item->get('created_date'); ?></span>
    </li>
</ul>