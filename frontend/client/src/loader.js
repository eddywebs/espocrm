/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014  Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 ************************************************************************/ 

(function (Espo, _, $) {

	var root = this;

	Espo.Loader = function (cache) {
		this.cache = cache || null;
		this._loadCallbacks = {};
	}

	_.extend(Espo.Loader.prototype, {

		cache: null,

		data: null,

		godClass: Espo,

		_loadCallbacks: null,

		_getClass: function (name) {			
			if (name in this.godClass) {
				return this.godClass[name];
			}
			return false;
		},

		_setClass: function (name, o) {
			this.godClass[name] = o;
		},

		_nameToPath: function (name) {
			var path;
			if (name.indexOf(':') != -1) {
				var arr = name.split(':');
				var name = arr[1];
				var mod = arr[0];
				if (mod == 'Custom') {
					path = 'client/custom/src/' + Espo.Utils.convert(name, 'C-h').split('.').join('/');
				} else {
					path = 'client/modules/' + Espo.Utils.convert(mod, 'C-h') + '/src/' + Espo.Utils.convert(name, 'C-h').split('.').join('/');
				}
			} else {
				path = 'client/src/' + Espo.Utils.convert(name, 'C-h').split('.').join('/');
			}
			path += '.js';
			return path;
		},

		_execute: function (script) {
			eval.call(root, script);
		},

		_executeLoadCallback: function (subject, o) {
			if (subject in this._loadCallbacks) {
				this._loadCallbacks[subject].forEach(function (callback) {
					callback(o);
				});
				delete this._loadCallbacks[subject];
			}
		},

		define: function (subject, dependency, callback) {
			var self = this;
			var proceed = function (relObj) {
				var o = callback.apply(this, arguments);								
				if (!o) {
					if (self.cache) {
						self.cache.clear('script', name);
					}
					throw new Error("Could not load '" + subject + "'");
				}
				self._setClass(subject, o);
				self._executeLoadCallback(subject, o);
			};

			if (!dependency) {
				proceed();
			} else {
				this.require(dependency, function () {
					proceed.apply(this, arguments);
				});
			}
		},

		require: function (subject, callback) {
			if (Object.prototype.toString.call(subject) === '[object Array]') {
				var list = subject;
			} else {
				this.load(subject, callback);
				return;
			}
			var totalCount = list.length;
			var readyCount = 0;
			var loaded = {};

			list.forEach(function (name) {
				this.load(name, function (c) {
					loaded[name] = c;
					readyCount++;
					if (readyCount == totalCount) {
						var args = [];
						for (var i in list) {
							args.push(loaded[list[i]]);
						}
						callback.apply(this, args);
					}
				});
			}.bind(this));
		},
		
		_addLoadCallback: function (name, callback) {
			if (!(name in this._loadCallbacks)) {
				this._loadCallbacks[name] = [];
			}
			this._loadCallbacks[name].push(callback);			
		},

		load: function (name, callback, error) {

			if (!name || name == '') {
				throw new Error("Can not load empty class name");
			}

			var c = this._getClass(name);
			
			if (c) {
				callback(c);
				return;
			}

			if (this.cache) {
				var script = this.cache.get('script', name);
				if (script) {					
					this._execute(script);

					var c = this._getClass(name);
					if (c) {
						callback(c);						
					}
					this._addLoadCallback(name, callback);
					return;
				}
			}

			var path = this._nameToPath(name);

			$.ajax({
				type: 'GET',
				cache: false,
				dataType: 'text',
				local: true,
				url: path,
				success: function (script) {
					if (this.cache) {
						this.cache.set('script', name, script);
					}

					this._addLoadCallback(name, callback);					
					this._execute(script);

					// TODO remove this and use define for all classes
					var c = this._getClass(name);
					if (c && typeof c === 'function') {
						this._executeLoadCallback(name, c);
					}
					return;
				}.bind(this),
				error: function () {
					if (typeof error == 'function') {
						error();
					}
					throw new Error("Could not load file '" + path + "'");
				}
			});
		},
		
		loadLib: function (url, callback) {
			if (this.cache) {
				var script = this.cache.get('script', url);
				if (script) {					
					this._execute(script);
					if (typeof callback == 'function') {
						callback();
					}
					return;
				}
			}
			
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'script',
				local: true,
				success: function () {
					if (typeof callback == 'function') {
						callback();
					}
				},
				error: function () {
					throw new Error("Could not load file '" + url + "'");
				},
			});
			
		},
	});

	Espo.loader = new Espo.Loader();
	Espo.require = function (subject, callback) {
		Espo.loader.require(subject, callback);
	}
	Espo.define = function (subject, dependency, callback) {
		Espo.loader.define(subject, dependency, callback);
	}	
	Espo.loadLib = function (url, callback) {
		Espo.loader.loadLib(url, callback);
	}

}).call(this, Espo, _, $);