{if isset($sliders[$block.id]) && is_array($sliders[$block.id]) && $sliders[$block.id]}
    <div id="block-slider" class="b-slider clearfix">
        <ul class="b-slider__slides" id="slider__slides_{$block.id}">
            {foreach $sliders[$block.id] as $item}
                <li class="b-slider__item{if $slider_positions[$block.id].slider_caption_hover} b-slider__item--hidden-caption{/if}"{if $slider_positions[$block.id].slider_custom_url} data-url="{$item.url}"{/if} style="width:{$slider_positions[$block.id].slider_thumb_w};height:{$slider_positions[$block.id].slider_thumb_h}">
                    {ia_image file=$item.image title=$item.name type='large' class="img-responsive"}
                    {if $slider_positions[$block.id].slider_caption}
                        <div class="b-slider__item__caption">
                            <h4>{$item.name}</h4>
                            <div class="b-slider__item__caption__text">{$item.body}</div>
                        </div>
                    {/if}
                </li>
            {/foreach}
        </ul>
        {if $slider_positions[$block.id].slider_pagination_nav}
            <div id="slider__pagination-nav_{$block.id}" class="b-slider__pagination-nav"></div>
        {/if}
        {if $slider_positions[$block.id].slider_direction_nav}
            <div class="b-slider__direction-nav">
                <a href="#" id="slider__direction-nav__prev_{$block.id}" class="b-slider__direction-nav__prev">prev</a>
                <a href="#" id="slider__direction-nav__next_{$block.id}" class="b-slider__direction-nav__next">next</a>
            </div>
        {/if}
    </div>


{*{if isset($sliders[$block.id]) && is_array($sliders[$block.id]) && $sliders[$block.id]}*}
        {*<div class="slider">*}
            {*<ul class="items">*}
                {*{foreach $sliders[$block.id] as $entry}{_v($entry)}*}
                    {*<li>*}
                        {*{if $entry.image}*}
                            {*{ia_image file=$entry.image title=$entry.title fullimage=true}*}
                        {*{/if}*}
                        {*<span class="banner">*}
							{*<span class="banner__title">*}
								{*{assign titlelems explode(" ", $entry.title)}*}
                                {*{foreach from=$titlelems item=titlelem}*}
                                    {*{if $titlelem@first}*}
                                        {*<span class="banner__title__strong">{$titlelem}</span>*}
                                    {*{else}*}
                                        {*{$titlelem}*}
                                    {*{/if}*}
                                {*{/foreach}*}
							{*</span>*}
                            {*</a>*}

						{*<span class="text2">{$entry.body|strip_tags|truncate:$config.sliderline_body_max:'...'}</span>*}
						{*<span class="text3">*}
						{*</span>*}
					{*</span>*}
                    {*</li>*}
                {*{/foreach}*}
            {*</ul>*}
            {*<ul class="pagination">*}
                {*{foreach $sliders[$block.id] as $entry}*}
                    {*<li class="a{$entry.id}"><a href="#">{$entry.title}</a></li>*}
                {*{/foreach}*}
            {*</ul>*}
            {*<div class="slider-bg"></div>*}
            {*<div class="link-box">*}
                {*<a class="b1" href="#">Kurdisch</a>*}
                {*<a class="b2" href="#">Deutsch</a>*}
                {*<a class="b3" href="#">Arabisch</a>*}
            {*</div>*}
        {*</div>*}
    {*{/if}*}

    {*{ia_print_js files='_IA_URL_modules/slider/js/front/jquery.carouFredSel.min'}*}

    {*{ia_add_js}*}
{*$(function() {*}
    {*$('#slider__slides_{$block.id}').carouFredSel({*}
        {*responsive: true,*}
        {*{if $slider_positions[$block.id].slider_direction_nav}prev: '#slider__direction-nav__prev_{$block.id}', next: '#slider__direction-nav__next_{$block.id}',{/if}*}
        {*{if $slider_positions[$block.id].slider_pagination_nav}pagination: '#slider__pagination-nav_{$block.id}',{/if}*}
        {*width: '{$slider_positions[$block.id].slider_width}',*}
        {*height: 'variable',*}
        {*direction: '{$slider_positions[$block.id].slider_direction}',*}
        {*items: {*}
            {*visible: {$slider_positions[$block.id].items_per_slide},*}
            {*height: 'variable'*}
        {*},*}
        {*scroll: {*}
            {*pauseOnHover: {$core.config.pause_on_hover},*}
            {*fx: '{$slider_positions[$block.id].slider_fx}',*}
            {*easing: '{$slider_positions[$block.id].slider_easing}',*}
            {*duration: {$slider_positions[$block.id].slider_scroll_duration}*}
        {*}*}
    {*});*}

    {*if($('#slider__slides_{$block.id} .b-slider__item--hidden-caption').length > 0) {*}
        {*$('#slider__slides_{$block.id} .b-slider__item--hidden-caption').hover(function () {*}
            {*$('#slider__slides_{$block.id} .b-slider__item__caption').stop(true, true).fadeToggle();*}
        {*});*}
    {*}*}

    {*{if $slider_positions[$block.id].slider_custom_url}*}
        {*$.each($('#slider__slides_{$block.id} .b-slider__item'), function() {*}
            {*if($(this).data('url').length > 0) {*}
                {*$(this).css('cursor', 'pointer');*}
                {*$(this).on('click', function (e) {*}
                    {*e.preventDefault();*}

                    {*window.open($(this).data('url'), '_blank');*}
                {*});*}
            {*}*}
        {*});*}
    {*{/if}*}

    {*$('#slider__slides_{$block.id} .b-slider__item').css('height', 'auto');*}
{*});*}
    {*{/ia_add_js}*}
{/if}