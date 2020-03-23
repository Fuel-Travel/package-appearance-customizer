<?php
return array(
    'heading_color' => array(
        'default' => '#111', // hex with #
        'targets' => array(
            '.entry-title',
            '.archive-title',
            '.entry-title a',
            '.archive-title a',
        ),
    ),
    'entry_title_size' => array(
        'default' => '40', // px
        'targets' => array(
            '.entry-title',
            '.archive-title',
        ),
    ),
    'entry_title_weight' => array(
        'default' => '300',
        'targets' => array(
            '.entry-title',
            '.archive-title',
        ),
    ),
    'entry_content_line_clamp' => array(
        'default' => '2', // line count
        'targets' => array(
            '.entry-content p',
        ),
        'font_size' => '16', // px
        'line_height' => '1.875', // em
    ),
);
?>