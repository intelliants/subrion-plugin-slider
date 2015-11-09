Ext.onReady(function()
{
	var pageUrl = intelli.config.admin_url + '/slider/';

	if (Ext.get('js-grid-placeholder'))
	{
		var urlParam = intelli.urlVal('status');

		intelli.slider =
		{
			columns: [
				'selection',
				{name: 'name', title: _t('name'), width: 2, editor: 'text'},
				{name: 'order', title: _t('order'), editor: 'number'},
				'status',
				{name: 'image', width: 35, sortable: false, renderer: function(value) {
						var fullimage = value.replace(/\.(\w+)$/i, "~.$1");
						return '<a href="' + intelli.config.ia_url + 'uploads/' + fullimage + '" rel="ia_lightbox[slider]"><i class="i-eye grid-icon" style="text" title="' + _t('view') + '"></i></a>';
					}
				},
				'update',
				'delete'
			],
			storeParams: urlParam ? {status: urlParam} : null,
			sorters: [{property: 'order', direction: 'ASC'}],
			url: pageUrl
		};

		intelli.slider = new IntelliGrid(intelli.slider, false);
		intelli.slider.toolbar = Ext.create('Ext.Toolbar', {items:
		[
			{
				emptyText: _t('text'),
				name: 'text',
				listeners: intelli.gridHelper.listener.specialKey,
				xtype: 'textfield'
			}, {
				displayField: 'title',
				editable: false,
				emptyText: _t('status'),
				id: 'fltStatus',
				name: 'status',
				store: intelli.slider.stores.statuses,
				typeAhead: true,
				valueField: 'value',
				xtype: 'combo'
			}, {
				handler: function(){intelli.gridHelper.search(intelli.slider);},
				id: 'fltBtn',
				text: '<i class="i-search"></i> ' + _t('search')
			}, {
				handler: function(){intelli.gridHelper.search(intelli.slider, true);},
				text: '<i class="i-close"></i> ' + _t('reset')
			}
		]});

		if (urlParam)
		{
			Ext.getCmp('fltStatus').setValue(urlParam);
		}

		intelli.slider.init();
	}
});