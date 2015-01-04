(function clApp($) {
	
	var appGlobal  = {
		// stores compiled tamplates
		templates: {
			formEmploymentDate: _.template($('#formEmploymentDateTemplate').html(), null, {variable: 'mo'}),
			formPatternSelectorSubshift: _.template($('#formPatternSelectorSubshiftTemplate').html(), null, {variable: 'data'}),
			formPatternInputOverview: _.template($('#formPatternInputOverviewTemplate').html(), null, {variable: 'mo'}),
			formPatternInputEdit: _.template($('#formPatternInputEditTemplate').html(), null, {variable: 'mo'}),
			formPatternInputDayIn: _.template($('#formPatternInputDayInTemplate').html(), null, {variable: 'mo'}),
			formPatternInputDayOff: _.template($('#formPatternInputDayOffTemplate').html(), null, {variable: 'mo'})
		}
	};
	
	var AppView = Backbone.View.extend({
		el: $('body'),
		urlRoot: window.document.URL,
		
		initialize: function clInitialize() {			
			this.formIdSelector = '#frm-baseaccountForm';
			
			this.user = new Zidane.User(this.screwfix.user, new Zidane.Acl(this.screwfix.acl.roles));
			
			this.date = new Zidane.Calendar();
			
			this.patternDate = new this.screwfix.common.ShiftPatternDate();
			
			var el = this.$el.find(this.formIdSelector)[0];			
			this.formView = new FormView({el: el,master: this, parent: this});
		}
	});	
	
	var FormView = Backbone.View.extend({
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// AppView
			this.parent = options.parent;
			
			var el = this.$el.find('#formEmployment')[0];
			this.employmentView = new EmploymentView({el: el, master: this.master, parent: this});			
			
			el = this.$el.find(this.master.formIdSelector + '-patternInput')[0];
			this.patternInputView = new PatternInputView({el: el, master: this.master, parent: this});
			
			el = this.$el.find('#formPatternSelector')[0];
			this.patternSelectorView = new PatternSelectorView({el: el, patternInputView: this.patternInputView, master: this.master, parent: this});
		},
		
		events: {
			"submit": "setPatternInput"
		},
		
		setPatternInput: function clSetPatternInput() {
			this.patternInputView.setPatternInput();
		}
	});
	
	var EmploymentView = Backbone.View.extend({
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// FormView
			this.parent = options.parent;
			
			var el = this.$el.find(this.master.formIdSelector + '-employmentLength')[0];
			this.employmentLengthView = new EmploymentLengthView({el: el, master: this.master, parent: this});
			
			el = this.$el.find('#formEmploymentDate')[0];
			this.employmentDateView = new EmploymentDateView({el: el, master: this.master, parent: this});
		}
	});
	
	var EmploymentLengthView = Backbone.View.extend({
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// EmploymentView
			this.parent = options.parent;
			
			this.borderYearsNumber = this.$el.attr('data-border-years-number');
		},
		
		events: {
			"change": "change"
		},
		
		change: function clChange() {
			this.parent.employmentDateView;
			if (this.$el.val() === 'full') {
				this.parent.employmentDateView.hide();
			}
			else {
				this.parent.employmentDateView.display();
			}
		}
	});
	
	var EmploymentDateView = Backbone.View.extend({
		template: appGlobal.templates.formEmploymentDate,
		isBuilt: false,
		$renderEl: null,		
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// EmploymentView
			this.parent = options.parent;
			
			if (this.el.hasChildNodes()) {
				this.isBuilt = true;
				this.$renderEl = $(this.el.firstChild);
			}			
		},
		
		render: function clRender() {
			this.$el.html(this.template({borderYearsNumber: this.parent.employmentLengthView.borderYearsNumber}));
			this.isBuilt = true;
		},
		
		display: function clDisplay() {
			if (!this.isBuilt) {
				this.render();
			}
			else {
				this.$el.append(this.$renderEl);
			}
		},
		
		hide: function clHide() {
			this.$renderEl = $(this.$el.children()[0]).detach();
		}
	});
	
	var PatternSelectorView = Backbone.View.extend({
		template: appGlobal.templates.formPatternSelectorSubshift,
		
		initialize: function clInitialize(options) {
			var that = this;
			
			// AppView
			this.master = options.master;
			
			// FormView
			this.parent = options.parent;
			
			this.patternInputView = options.patternInputView;
			
			this.subshiftsMap = this.screwfix.subshiftsMap;
			
			this.$shift = this.$el.find(this.master.formIdSelector + '-sysPatternShiftSelect');
			
			this.$shift.change(changeShift);			
			
			this.$subshiftParent = $(this.$el.children()[1]);			
			this.set$subshift();
			
			function changeShift() {
				that.changeShift(); 
			}
			
			this.$shiftOptionCustom = this.$shift.children("option[value='0']");
		},
		
		set$subshift: function clSet$subshift() {
			var that = this;
			
			this.$subshift = this.$subshiftParent.children(this.master.formIdSelector + '-sysPatternSubshiftSelect');
			
			if (this.$subshift.length) {this.$subshift.change(changeSubshift);
			}			
			
			function changeSubshift() {
				that.changeSubshift()
			}			
		},
		
		changeShift: function clChangeShift() {
			var shiftId = this.$shift.val();
			var subshiftId = _.isUndefined(this.subshiftsMap[shiftId]) ? 0 : _.keys(this.subshiftsMap[shiftId])[0];
			var id = shiftId + ':' + subshiftId;
			
			this.removeSubshift();
			
			if (subshiftId) {
				this.renderSubshift(shiftId);
			}
			
			this.patternInputView.changeOverview(id);
			
			this.unsetOptionCustom();
		},
		
		changeSubshift: function clChangeSubshift() {
			var shiftId = this.$shift.val();
			var subshiftId = this.$subshift.val();
			var id = shiftId + ':' + subshiftId;
			
			this.patternInputView.changeOverview(id);
		},
		
		renderSubshift: function clRenderSubshift(shiftId) {
			var data = this.subshiftsMap[shiftId];
			
			this.$subshiftParent.html(this.template(data));
			this.$subshiftParent.addClass('formRowNolabel');
			
			this.set$subshift();
		},
		
		removeSubshift: function clRemoveSubshift() {
			this.$subshiftParent.empty()
				.removeClass();
		},
		
		setOptionCustom: function clSetOptionCustom() {
			if (this.$shiftOptionCustom.length === 0) {
				this.$shiftOptionCustom = Zidane.create('option', null, {value: '0'}).text('Custom');

				this.$shift.prepend(this.$shiftOptionCustom);
			}
			
			this.$shiftOptionCustom.prop('selected', true);
			
			this.removeSubshift();
		},
		
		unsetOptionCustom: function clUnsetOptionCustom() {
			if (_.isUndefined(this.screwfix.patternsMap['0:0'])) {
				this.$shiftOptionCustom.remove();
				
				this.$shiftOptionCustom = $();
			}
		}
	});
	
	var PatternInputView = Backbone.View.extend({
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// FormView
			this.parent = options.parent;
			
			this.patternInputOverviewView = new PatternInputOverviewView({el: this.el, master: this.master, parent: this});
			
			this.patternInputEditView = new PatternInputEditView({el: this.el, master: this.master, parent: this});
		},
		
		/**
		 * @param {string} id id of pattern eg '2:1'
		 */
		changeOverview: function clChangeOverview(id) {			
			if (_.isUndefined(this.screwfix.patternsMap[id])) {
				// do nothing
				return;
			}
			
			this.patternInputOverviewView.change(this.screwfix.patternsMap[id]);
		},
		
		/**
		 * Sets pattern input from values of instance of PatternInputEditView
		 * @returns {this}
		 */
		setPatternInput: function clSetPatternInput() {
			this.patternInputEditView.setPatternInput();
			
			return this;
		}
	});
	
	var PatternInputOverviewView = Backbone.View.extend({
		template: appGlobal.templates.formPatternInputOverview,
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// PatternInputView
			this.parent = options.parent;
			
			this.patternDate = this.master.patternDate;
		},
		
		events: {
			"click input[name='customize']": "customize"
		},
		
		/**
		 * @param {array} pattern
		 */
		render: function clRender(pattern) {
			this.$el.empty();
			
			var date = this.master.date.clone();
			date.today().startWeek();
			
			var inputPattern = {pattern: pattern, firstDay: date.toString()};
			
			var patternStr = JSON.stringify(inputPattern);
			
			date.setFormat(function clSetFormat(y, m, d, o) {
					return d + ' ' + Zidane.capitalize(o.getMonthString());
			});

			this.$el.html(this.template({pattern: pattern, patternStr: patternStr, date: date}));
			
			this.afterRender();
			
			return this;
		},
		
		afterRender: function clAfterRender() {
			this.delegateEvents();
		},
		
		/**
		 * Changes overview by given pattern.
		 * @param {array} pattern raw unadjusted pattern
		 * @returns {this}
		 */
		change: function clChange(pattern) {
			var adjustedPattern = this.patternDate.adjust(pattern);
			
			this.render(adjustedPattern);
			
			return this;
		},
		
		customize: function clCustomize() {
			this.undelegateEvents();
			
			var patternInputValue = this.$el.find("input[name='patternInput']").val();
			
			var pattern = $.parseJSON(patternInputValue).pattern;
			
			this.$el.empty();
			
			this.parent.patternInputEditView.render(pattern);
		}
	});
	
	var PatternInputEditView = Backbone.View.extend({
		template: appGlobal.templates.formPatternInputEdit,
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// PatternInputView
			this.parent = options.parent;
			
			this.patternDate = this.master.patternDate;
			
			// array of objects containing "from to" times and date
			this.daysData = [];
			
			this.days = [];
			
			this.isActive = false;
			
			this.$input = null;
		},
		
		events: {
			"click input[name='uncustomize']": "uncustomize",
			"click input[name='addWeek']": "addWeek",
			"click input[name='removeWeek']": "removeWeek"
		},
		
		render: function clRender(pattern) {
			var date = this.master.date.clone();
			date.today().startWeek();
			
			var inputPattern = {pattern: pattern, firstDay: date.toString()};
			
			var patternStr = JSON.stringify(inputPattern);
			
			date.setFormat(function clSetFormat(y, m, d, o) {
					return d + ' ' + Zidane.capitalize(o.getMonthString());
			});
			
			// dates will be filled in the template script
			this.daysData = [];
			
			this.days = [];
			
			this.$el.html(this.template({pattern: pattern, patternStr: patternStr, date: date, view: this}));
			
			var that = this;
			
			this.$el.find("td.day").each(function clBuildPatternDayView(i) {
				var day = new PatternDayView({el: this, master: that.master, parent: that, data: that.daysData[i]});
				that.days.push(day);
				that.listenTo(day, 'changed', that.changed);
			});
			
			this.afterRender();			
				
			return this;
		},
		
		afterRender: function clAfterRender() {
			this.delegateEvents();
			
			this.isActive = true;
			this.$input = this.$el.find("input[name='patternInput']");
		},
		
		uncustomize: function clUncustomize() {			
			this.setPatternInput();
			
			var patternInputValue = this.$input.val();
			
			var pattern = $.parseJSON(patternInputValue).pattern;
			
			this.isActive = false;
			
			this.clear();
			
			this.parent.patternInputOverviewView.render(pattern);
		},
		
		/**
		 * Collects pattern from times kept in pattern day views.
		 * @returns {Array}
		 */
		getPattern: function clGetPattern() {
			var pattern = [];
			for(var i=0, w=-1; i<this.days.length; i++) {
				if (i%7 === 0) {
					w++;
					pattern[w] = [];
				}
				pattern[w].push(this.days[i].getTimes());
			}
			
			return pattern;
		},
		
		/**
		 * Sets input pattern value from times collected from PatternDayView instances
		 * @returns {this}
		 */
		setPatternInput: function clSetPatternInput() {
			if (this.isActive) {
				var date = this.days[0].getDate();
				var firstDay = new Zidane.Calendar(date.getYear(), date.getMonth(), date.getDate());
				
				var patternInput = {pattern: this.getPattern(), firstDay: firstDay.toString()};

				this.$input.val(JSON.stringify(patternInput));
			}
			
			return this;
		},
		
		addWeek: function clAddWeek() {
			var pattern = this.getPattern();
			var w = pattern.length;
			
			pattern[w] = [];
			
			for (var i=0; i<5; i++) {
				pattern[w].push(['00:00', '00:00']);
			}
			
			for (var i=0; i<2; i++) {
				pattern[w].push('off');
			}
			
			this.render(pattern);
			
			this.changed();
			
			return this;
		},
		
		removeWeek: function clRemoveWeek() {
			var pattern = this.getPattern();
			
			if (pattern.length > 1) {
				// removes last week
				pattern.pop();
			}
			
			this.render(pattern);
			
			this.changed();
			
			return this;
		},
		
		changed: function clChanged() {
			this.master.formView.patternSelectorView.setOptionCustom();
		},
		
		clear: function clClear() {
			this.undelegateEvents();
			this.stopListening();
			for (var i=0; i<this.days.length; i++) {
				this.days[i].remove();
			}
			this.$input = null;
			this.days = [];
			this.$el.empty();
		}
	});
	
	/**
	 * @trigger changed() when new time is selected or changed state of day view
	 */
	var PatternDayView = Backbone.View.extend({
		templateIn: appGlobal.templates.formPatternInputDayIn,
		templateOff: appGlobal.templates.formPatternInputDayOff,
		
		initialize: function clInitialize(options) {
			// AppView
			this.master = options.master;
			// PatternInputEditView
			this.parent = options.parent;
			
			this.date = options.data.date;
			
			this.times = options.data.times;
			
			this.state = options.data.times == 'off' ? 'off' : 'in';
			
			this.render();
		},
		
		events: {
			"click a": "changeState",
			"change select": "setTimes"
		},
		
		render: function clRender() {
			if (this.state === 'in') {
				this.renderIn();
			}
			else {
				this.renderOff();
			}
			
			return this;
		},
		
		renderIn: function clRenderIn() {
			this.$el.attr('class', 'day dayIn');
			this.$el.html(this.templateIn({date: this.date, times: this.times, view: this}));
			
			this.$selects = this.$el.find('select');
			
			return this;
		},
		
		renderOff: function clRenderOff() {
			this.$el.attr('class', 'day dayOff');
			this.$el.html(this.templateOff({date: this.date, times: this.times, view: this}));
			
			this.$selects = null;
			
			return this;
		},
		
		changeState: function clChangeState() {
			this.state = this.state === 'in' ? 'off' : 'in';
			
			if (this.state === 'in') {
				if (!this.times) {
					this.setDefaultTimes();
				}
				this.renderIn();
			}
			else {
				this.times = 'off';
				this.renderOff();
			}
			
			this.trigger('changed');
			
			return this;
		},
		
		/**
		 * Sets times from selects. If off day then it sets times to 'off'
		 * @returns {this}
		 */
		setTimes: function clSetTimes() {
			if (this.$selects) {
				this.times = [];
				this.times[0] = $(this.$selects[0]).val() + ':' + $(this.$selects[1]).val();
				this.times[1] = $(this.$selects[2]).val() + ':' + $(this.$selects[3]).val();
			}
			else {
				this.times = 'off';
			}
			
			this.trigger('changed');
			
			return this;
		},
		
		setDefaultTimes: function clSetDefaultTimes() {
			this.times = ['00:00', '00:00'];
			
			return this;
		},
		
		getTimes: function clGetTimes() {
			return this.times;
		},
		
		getDate: function clGetDate() {
			return this.date;
		},
		
		/**
		 * Helper function for template. It pads time.
		 * @param {integer} timeUnit hours or minutes
		 * @returns {string|integer}
		 */
		padTime: function clPadTime(timeUnit) {
			if (timeUnit < 10) {
				timeUnit = '0' + timeUnit;
			}
			
			return timeUnit;
		},
		
		/**
		 * Helper function for template.
		 * @param {integer} t time unit either hour or minute
		 * @param {string} tString eg '07:00'
		 * @param {string} type type of time unit either 'h' or 'm'
		 * @returns {string}
		 */
		selectOption: function clSelectOption(t, tString, type) {			
			var time = tString.split(':');
			time = {
				h: time[0],
				m: time[1]
			};
			
			return time[type] == t ? 'selected="selected"' : '';
		}
	});
	
	//create instance of master view
	var app = new AppView();
	
})(jQuery);
