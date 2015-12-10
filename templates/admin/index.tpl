{if (iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction) && $positions}
	<form action="" method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}
	
		<div class="wrap-list">
			<div class="wrap-group">
				<div class="wrap-group-heading">
					<h4>{lang key='options'}</h4>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="js-slider-position">{lang key='slider_block'} <span class="required">*</span></label>
					<div class="col col-lg-4">
						<select name="position" id="js-slider-position">
							{foreach $positions as $position}
								<option value="{$position.id}" data-width="{$position.slider_width}" data-height="{$position.slider_height}"
									{if $position.id == $slides.position} selected="selected"{/if}>
									{$position.position}: {if $position.title}{$position.title}{else}{lang key='without_title'}{/if}
								</option>
							{/foreach}
						</select>
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-image">{lang key='image'} <span class="required">*</span></label>
					<div class="col col-lg-4">
						{if isset($slides.image) && !empty($slides.image)}
							<div class="input-group thumbnail thumbnail-single with-actions">
								<a href="{printImage imgfile=$slides.image fullimage=true url=true}" rel="ia_lightbox">
									{printImage imgfile=$slides.image}
								</a>
							</div>
						{/if}

						{ia_html_file name='image' id='input-image'}
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-url">{lang key='url'}</label>
					<div class="col col-lg-4">
						<input type="text" name="url" value="{$slides.url}" id="input-url">
					</div>
				</div>

				<div class="row">
					<ul class="nav nav-tabs">
						{foreach $core.languages as $code => $language}
							<li{if $language@iteration == 1} class="active"{/if}><a href="#tab-language-{$code}" data-toggle="tab" data-language="{$code}">{$language.title}</a></li>
						{/foreach}
					</ul>

					<div class="tab-content">
						{foreach $core.languages as $code => $language}
							<div class="tab-pane{if $language@first} active{/if}" id="tab-language-{$code}">
								<div class="row">
									<label class="col col-lg-2 control-label">{lang key='name'} <span class="required">*</span></label>
									<div class="col col-lg-4">
										<input type="text" name="names[{$code}]" value="{if isset($slides.names) && is_array($slides.names)}{$slides.names.$code|escape:'html'}{/if}">
									</div>
								</div>
								<div class="row js-local-url-field">
									<label class="col col-lg-2 control-label">{lang key='body'}</label>
									<div class="col col-lg-8">
										{if isset($slides.bodies) && is_array($slides.bodies)}
											{assign value $slides.bodies.$code}
										{else}
											{assign value ''}
										{/if}
										{ia_wysiwyg name="bodies[{$code}]" value=$value}
									</div>
								</div>
							</div>
						{/foreach}
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-order">{lang key='order'}</label>
					<div class="col col-lg-4">
						<input type="number" name="order" value="{$slides.order}" id="input-order">
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-status">{lang key='status'}</label>
					<div class="col col-lg-4">
						<select name="status" id="input-status">
							<option value="active"{if iaCore::STATUS_ACTIVE == $slides.status} selected="selected"{/if}>{lang key='active'}</option>
							<option value="inactive"{if iaCore::STATUS_INACTIVE == $slides.status} selected="selected"{/if}>{lang key='inactive'}</option>
						</select>
					</div>
				</div>
			</div>

			<div class="form-actions inline">
				<button type="submit" name="save" class="btn btn-primary">{if iaCore::ACTION_EDIT == $pageAction}{lang key='save_changes'}{else}{lang key='add'}{/if}</button>
			</div>
		</div>
	</form>
{/if}