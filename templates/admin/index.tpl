{if (iaCore::ACTION_ADD == $pageAction || iaCore::ACTION_EDIT == $pageAction) && $positions}
	<form action="" method="post" enctype="multipart/form-data" class="sap-form form-horizontal">
	{preventCsrf}
	
		<div class="wrap-list">
			<div class="wrap-group">
				<div class="wrap-group-heading">
					<h4>{lang key='options'}</h4>
				</div>
				
				<div class="row">
					<label class="col col-lg-2 control-label" for="input-language">{lang key='language'} <span class="required">*</span></label>
					<div class="col col-lg-4">
						<select name="lang" id="input-language"{if count($core.languages) == 1} disabled="disabled"{/if}>
						{foreach $core.languages as $code => $language}
							<option value="{$code}"{if $slides.lang == $code} selected="selected"{/if}>{$language.title}</option>
						{/foreach}
						</select>
					</div>
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
					<label class="col col-lg-2 control-label" for="input-name">{lang key='name'} <span class="required">*</span></label>
					<div class="col col-lg-4">
						<input type="text" name="name" value="{$slides.name|escape:'html'}" id="input-name">
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
					<label class="col col-lg-2 control-label" for="body">{lang key='body'}</label>
					<div class="col col-lg-8">
						{ia_wysiwyg name='body' value=$slides.body}
					</div>
				</div>

				<div class="row">
					<label class="col col-lg-2 control-label" for="input-order">{lang key='order'}</label>
					<div class="col col-lg-4">
						<input type="text" name="order" value="{$slides.order|escape:'html'}" id="input-order">
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