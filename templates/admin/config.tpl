<div class="wrap-list">
    {foreach $positions as $position name='positions_foreach'}
        <fieldset class="wrap-group" id="position-{$position}">
            <div class="wrap-group-heading">
                <h4>{$position}</h4>
            </div>
            <a class="btn btn-primary" id="block_{$position}" href="{$smarty.const.IA_SELF}?action=add_block&pos={$position}&num={if isset($slider_blocks.$position)}{$slider_blocks.$position|@count}{/if}"><i class="i-plus-alt"></i> {lang key="add_slider_block"}</a>
            {if isset($slider_blocks.$position)}
                <div class="plates-list sap-form">
                    {foreach from=$slider_blocks.$position item="block" name="block"}
                        <div class="slider-block col col-lg-2" id="block_{$block.id}">
                            <h4>
                                {if $block.title}{$block.title}{else}{lang key='without_title'}{/if}
                                <a href="{$smarty.const.IA_ADMIN_URL}blocks/edit/{$block.id}/">
                                    <i class="i-edit"></i>
                                </a>
                            </h4>
                            <p><span class="text-small text-muted">{lang key='items_per_slide'}:</span><span class="right"><input class="items_per_slide" type="text" value="{$blocks_options[$block.id].items_per_slide}"></span></p>
                            {*<p>*}
                                {*<span class="text-small text-muted">{lang key='slider_direction'}:</span>*}
                                {*<span class="right">*}
                                    {*<select class="slider_direction">*}
                                        {*{foreach $config_options.slider_direction as $direction}*}
                                        {*<option value="{$direction}"{if $blocks_options[$block.id].slider_direction == $direction} selected="selected"{/if}>{$direction}</option>*}
                                        {*{/foreach}*}
                                    {*</select>*}
                                {*</span>*}
                            {*</p>*}
                            <p>
                                <span class="text-small text-muted">{lang key='slider_fx'}:</span>
                                <span class="right">
                                    <select class="slider_fx">
                                        {foreach $config_options.slider_fx as $fx}
                                        <option value="{$fx}"{if $blocks_options[$block.id].slider_fx == $fx} selected="selected"{/if}>{$fx}</option>
                                        {/foreach}
                                    </select>
                                </span>
                            </p>
                            {*<p>*}
                                {*<span class="text-small text-muted">{lang key='slider_easing'}:</span>*}
                                {*<span class="right">*}
                                    {*<select class="slider_easing">*}
                                        {*{foreach $config_options.slider_easing as $easing}*}
                                        {*<option value="{$easing}"{if $blocks_options[$block.id].slider_easing == $easing} selected="selected"{/if}>{$easing}</option>*}
                                        {*{/foreach}*}
                                    {*</select>*}
                                {*</span>*}
                            {*</p>*}
                            {*<p><span class="text-small text-muted">{lang key='slider_scroll_duration'}:</span><span class="right"><input class="slider_scroll_duration" type="text" value="{$blocks_options[$block.id].slider_scroll_duration}"></span></p>*}
                            <p><span class="text-small text-muted">{lang key='slider_autoplay'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_autoplay name='slider_autoplay'}</span></p>
                            <p><span class="text-small text-muted">{lang key='slider_autoplay_timeout'}:</span><span class="right"><input class="slider_autoplay_timeout" type="text" value="{$blocks_options[$block.id].slider_autoplay_timeout}"></span></p>
                            <p><span class="text-small text-muted">{lang key='slider_loop'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_loop name='slider_loop'}</span></p>
                            <p><span class="text-small text-muted">{lang key='slider_margin'}:</span><span class="right"><input class="slider_margin" type="text" value="{$blocks_options[$block.id].slider_margin}"></span></p>
                            <p><span class="text-small text-muted">{lang key='slider_direction_nav'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_direction_nav name='slider_direction_nav'}</span></p>
                            <p><span class="text-small text-muted">{lang key='slider_pagination_nav'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_pagination_nav name='slider_pagination_nav'}</span></p>
                            <p><span class="text-small text-muted">{lang key='slider_caption'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_caption name='slider_caption'}</span></p>
                            <p><span class="text-small text-muted">{lang key='slider_caption_hover'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_caption_hover name='slider_caption_hover'}</span></p>
                            <p><span class="text-small text-muted">{lang key='slider_custom_url'}:</span><span class="right">{html_radio_switcher value=$blocks_options[$block.id].slider_custom_url name='slider_custom_url'}</span></p>
                            <p class="actions">
                                <input class="btn btn-sm btn-success save-block" type="button" name="save" value="{lang key='save'}">
                                <input class="btn btn-sm btn-danger delete-block" type="button" name="delete" value="{lang key='delete'}">
                            </p>
                        </div>
                    {/foreach}
                </div>
            {else}
                <span class="alert">{lang key="no_slider_blocks"}</span>
            {/if}
        </fieldset>
    {/foreach}
</div>

{ia_print_js files="_IA_URL_modules/slider/js/admin/config"}