(function() {
	'use strict';

	function renderNotice() {
		var config = window.GFMissingExtensionsNoticeConfig;
		if ( ! config ) {
			return;
		}

		document.querySelectorAll('.gf-missing-ext-notice').forEach(function(node){
			node.remove();
		});

		var form = window.form;
		if(!form || !Array.isArray(form.fields)){
			return;
		}

		var registered = new Set(config.registeredFieldTypes || []);
		var knownFieldTypeMarkers = Object.create(null);
		var missing = [];
		var seen = Object.create(null);

		function add(name, fieldId){
			if(!name){
				return;
			}

			if(!seen[name]){
				seen[name] = {
					name: name,
					fieldIds: []
				};
				missing.push(seen[name]);
			}

			if(fieldId !== undefined && fieldId !== null){
				if(seen[name].fieldIds.indexOf(fieldId) === -1){
					seen[name].fieldIds.push(fieldId);
				}
			}
		}

		function getSavedFieldType(field){
			if(!field || typeof field !== 'object'){
				return '';
			}

			return String(field.type || field.inputType || '');
		}

		function structureHasKeyMatch(data, needle, mode){
			if(!data || typeof data !== 'object'){
				return false;
			}

			if(Array.isArray(data)){
				for(var i = 0; i < data.length; i++){
					if(structureHasKeyMatch(data[i], needle, mode)){
						return true;
					}
				}

				return false;
			}

			for(var key in data){
				if(!Object.prototype.hasOwnProperty.call(data, key)){
					continue;
				}

				if((mode === 'prefix' && key.indexOf(needle) === 0) || (mode === 'contains' && key.indexOf(needle) !== -1)){
					return true;
				}

				if(structureHasKeyMatch(data[key], needle, mode)){
					return true;
				}
			}

			return false;
		}

		function formMatchesMarker(info){
			if(info.type === 'field_type'){
				return (form.fields || []).some(function(field){
					return getSavedFieldType(field) === info.marker;
				});
			}

			if(info.type === 'key_prefix'){
				return structureHasKeyMatch(form, info.marker, 'prefix');
			}

			if(info.type === 'key_contains'){
				return structureHasKeyMatch(form, info.marker, 'contains');
			}

			return false;
		}

		(config.serverMissing || []).forEach(function(name){
			add(name);
		});

		(config.markers || []).forEach(function(info){
			if(info.type === 'field_type' && info.marker){
				knownFieldTypeMarkers[info.marker] = true;
			}

			if(info.active){
				return;
			}

			var matchedFieldIds = [];
			(form.fields || []).forEach(function(field){
				var match = false;
				if(info.type === 'field_type'){
					match = getSavedFieldType(field) === info.marker;
				} else if(info.type === 'key_prefix'){
					match = structureHasKeyMatch(field, info.marker, 'prefix');
				} else if(info.type === 'key_contains'){
					match = structureHasKeyMatch(field, info.marker, 'contains');
				}
				if(match && field.id !== undefined && field.id !== null){
					matchedFieldIds.push(Number(field.id));
				}
			});

			if(matchedFieldIds.length > 0){
				matchedFieldIds.forEach(function(fieldId){
					add(info.name, fieldId);
				});
			} else {
				if(formMatchesMarker(info)){
					add(info.name);
				}
			}
		});

		(form.fields || []).forEach(function(field){
			var fieldType = getSavedFieldType(field);
			if(!fieldType || registered.has(fieldType) || knownFieldTypeMarkers[fieldType]){
				return;
			}

			var label = config.fieldTypeLabel.replace('%s', fieldType);
			if(field.id !== undefined && field.id !== null){
				add(label, Number(field.id));
			} else {
				add(label);
			}
		});

		var formattedMissing = missing.map(function(entry){
			if(entry.fieldIds && entry.fieldIds.length > 0){
				var sortedIds = entry.fieldIds.slice().sort(function(a, b){
					return a - b;
				});

				if(sortedIds.length === 1){
					var suffix = (config.usedByField || '').replace('%s', sortedIds[0]);
					return entry.name + (suffix ? ' ' + suffix : '');
				} else {
					var idsString = sortedIds.join(', ');
					var suffix = (config.usedByFields || '').replace('%s', idsString);
					return entry.name + (suffix ? ' ' + suffix : '');
				}
			}
			return entry.name;
		});

		if(!formattedMissing.length){
			return;
		}

		var toolbar = document.querySelector('#gform-form-toolbar');
		var wrap = document.querySelector('#wpbody-content > .wrap.gforms_edit_form') || document.querySelector('#wpbody-content > .wrap');
		var sidebar = document.querySelector('.editor-sidebar');
		if(!toolbar && !wrap && !sidebar){
			return;
		}

		var toolbarOrigTop = toolbar ? toolbar.getBoundingClientRect().top : 0;
		var toolbarOrigLeft = toolbar ? toolbar.getBoundingClientRect().left : 0;
		var wrapOrigTop = wrap ? wrap.getBoundingClientRect().top : 0;
		var wrapOrigHeight = wrap ? wrap.getBoundingClientRect().height : 0;
		var sidebarOrigTop = sidebar ? sidebar.getBoundingClientRect().top : 0;

		var notice = document.createElement('div');
		notice.className = 'notice notice-warning gf-missing-ext-notice';
		notice.style.top = toolbarOrigTop + 'px';
		notice.style.left = toolbarOrigLeft + 'px';

		var paragraph = document.createElement('p');
		paragraph.style.margin = '0';
		var strong = document.createElement('strong');
		strong.textContent = formattedMissing.length > 1 ? config.noticeLabelPlural : config.noticeLabelSingular;
		paragraph.appendChild(strong);
		paragraph.appendChild(document.createTextNode(' ' + formattedMissing.join(', ')));
		notice.appendChild(paragraph);

		document.body.appendChild(notice);

		var offset = Math.ceil(notice.getBoundingClientRect().height);

		if(toolbar){
			toolbar.style.top = (toolbarOrigTop + offset) + 'px';
		}

		if(wrap){
			wrap.style.top = (wrapOrigTop + offset) + 'px';
			wrap.style.height = Math.max(wrapOrigHeight - offset, 0) + 'px';
		}

		if(sidebar){
			sidebar.style.top = (sidebarOrigTop + offset) + 'px';
		}
	}

	if(document.readyState === 'complete'){
		window.setTimeout(renderNotice, 0);
		return;
	}

	window.addEventListener('load', renderNotice, { once: true });
})();
