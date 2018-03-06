{if isset($sliders[$block.id]) && is_array($sliders[$block.id]) && $sliders[$block.id]}
    <div id="block-slider" class="b-slider clearfix">
        <div class="owl-carousel owl-theme" id="slider__slides_{$block.id}">
            {foreach $sliders[$block.id] as $item}
                <div class="slider__url{if $slider_positions[$block.id].slider_caption_hover} b-slider__item--hidden-caption{/if}" {if $slider_positions[$block.id].slider_custom_url} data-url="{$item.url}"{/if}>
                    {ia_image file=$item.image title=$item.name type='large'}
                    {if $slider_positions[$block.id].slider_caption}
                        <div class="b-slider__item__caption">
                            <h4>{$item.name}</h4>
                            <div class="b-slider__item__caption__text">{$item.body}</div>
                        </div>
                    {/if}
                </div>
            {/foreach}
        </div>
    </div>

    {ia_print_js files='_IA_URL_modules/slider/js/front/owl.carousel.min'}

    {ia_add_js}
        $(function() {
            var owlOptions = {
                items: {$slider_positions[$block.id].items_per_slide},
                margin: {$slider_positions[$block.id].slider_margin},
                loop: {$slider_positions[$block.id].slider_loop},
                animateOut: '{$slider_positions[$block.id].slider_fx}' + 'Out',
                animateIn: '{$slider_positions[$block.id].slider_fx}' + 'In',
                dots: {$slider_positions[$block.id].slider_pagination_nav},
                dotsEach: true,
                autoplayTimeout: {$slider_positions[$block.id].slider_autoplay_timeout},
                autoplayHoverPause: {$core.config.pause_on_hover},
                autoplay: {$slider_positions[$block.id].slider_autoplay},
                nav: {$slider_positions[$block.id].slider_direction_nav},
                navText: ['<span class="fa fa-angle-left"></span>','<span class="fa fa-angle-right"></span>']
            }

            $('#slider__slides_{$block.id}').owlCarousel(owlOptions);

            {if $slider_positions[$block.id].slider_custom_url}

                $('#slider__slides_{$block.id} .slider__url').hover(
                    function() {
                        if ($(this).data('url').length > 0) {
                            $(this).css('cursor', 'pointer');
                        }
                    }
                );

                $('#slider__slides_{$block.id} .slider__url').on('click', function (e) {
                    if ($(this).data('url').length > 0) {
                        e.preventDefault();
                        window.open($(this).data('url'), '_blank');
                    }
                })
            {/if}

            if($('#slider__slides_{$block.id} .b-slider__item--hidden-caption').length > 0) {
                $('#slider__slides_{$block.id} .b-slider__item--hidden-caption').hover(function () {
                    $('#slider__slides_{$block.id} .b-slider__item__caption').stop(true, true).fadeToggle();
                });
            }
        });
    {/ia_add_js}
{/if}