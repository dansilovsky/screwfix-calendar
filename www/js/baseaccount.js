(function($) {
	
	var appGlobal  = {
		// stores compiled tamplates
		templates: {
			formEmploymentDate: _.template($('#formEmploymentDateTemplate').html(), null, {variable: 'mo'}),
			formPatternInputOverview: _.template($('#formPatternInputOverviewTemplate').html(), null, {variable: 'mo'}),
			formPatternInputEdit: _.template($('#formPatternInputEditTemplate').html(), null, {variable: 'mo'}),
			formPatternInputDayIn: _.template($('#formPatternInputDayInTemplate').html(), null, {variable: 'mo'}),
			formPatternInputDayOff: _.template($('#formPatternInputDayOffTemplate').html(), null, {variable: 'mo'})
		}
	};
	
	var AppView = Backbone.View.extend({
		el: $('body'),
		urlRoot: window.document.URL,
		
		initialize: function() {			
			this.formIdSelector = '#frm-baseaccountForm';
			
			this.user = new Zidane.User(this.screwfix.user, new Zidane.Acl(this.screwfix.acl.roles));
			
			this.date = new Zidane.Calendar();
			
			this.patternDate = new this.screwfix.common.ShiftPatternDate();
			
			var el = this.$el.find(this.formIdSelector)[0];			
			this.formView = new FormView({el: el,master: this, parent: this});
		}
	});	
	
	var FormView = Backbone.View.extend({
		
		initialize: function(options) {
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
		
		setPatternInput: function() {
			this.patternInputView.setPatternInput();
		}
	});
	
	var EmploymentView = Backbone.View.extend({
		
		initialize: function(options) {
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
		
		initialize: function(options) {
			// AppView
			this.master = options.master;
			// EmploymentView
			this.parent = options.parent;
			
			this.borderYearsNumber = this.$el.attr('data-border-years-number');
		},
		
		events: {
			"change": "change"
		},
		
		change: function() {
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
		
		initialize: function(options) {
			// AppView
			this.master = options.master;
			// EmploymentView
			this.parent = options.parent;
			
			if (this.el.hasChildNodes()) {
				this.isBuilt = true;
				this.$renderEl = $(this.el.firstChild);
			}			
		},
		
		render: function() {
			this.$el.html(this.template({borderYearsNumber: this.parent.employmentLengthView.borderYearsNumber}));
			this.isBuilt = true;
		},
		
		display: function() {
			if (!this.isBuilt) {
				this.render();
			}
			else {
				this.$el.append(this.$renderEl);
			}
		},
		
		hide: function() {
			this.$renderEl = $(this.$el.children()[0]).detach();
		}
	});
	
	var PatternSelectorView = Backbone.View.extend({
		
		initialize: function(options) {
			var that = this;
			
			// AppView
			this.master = options.master;
			
			// FormView
			this.parent = options.parent;
			
			this.patternInputView = options.patternInputView;
			
			this.$team = this.$el.find(this.master.formIdSelector + '-sysPatternTeamSelect');
			
			this.$shift = this.$el.find(this.master.formIdSelector + '-sysPatternShiftSelect');
			
			this.prevTeamVal = this.$team.val();
			this.prevShiftVal = this.$shift.val();
			
			this.$team.change(change);
			this.$shift.change(change);
			
			function change() {
				that.change(); 
			}
			
			this.$teamOptionCustom = this.$team.children("option[value='0']");
			this.$shiftOptionCustom = this.$shift.children("option[value='0']");
		},
		
		change: function() {
			var teamId = this.$team.val();
			var shiftId = this.$shift.val();
			
			if (this.prevTeamVal != 0 && this.prevShiftVal != 0) {
				if (teamId == 0) {
					this.$shift.children("option[value='0']").prop('selected', true);
				}
				
				if (shiftId == 0) {
					this.$team.children("option[value='0']").prop('selected', true);
				}
			}
			else if (this.prevTeamVal == 0 && this.prevShiftVal == 0) {
				if (teamId > 0) {
					this.$shift.children("option[value='1']").prop('selected', true);
				}
				
				if (shiftId > 0) {
					this.$team.children("option[value='1']").prop('selected', true);
				}
			}
				
			this.unsetOptionCustom();			
			
			this.prevTeamVal = teamId = this.$team.val();
			this.prevShiftVal = shiftId = this.$shift.val();
			
			var id = teamId == 0 || shiftId == 0 ? '0:0' : teamId + ':' + shiftId;
			
			this.patternInputView.changeOverview(id);
		},
		
		setOptionCustom: function() {
			if (this.$teamOptionCustom.length === 0) {
				this.$teamOptionCustom = Zidane.create('option', null, {value: '0'}).text('Custom');
				this.$shiftOptionCustom = Zidane.create('option', null, {value: '0'}).text('Custom');

				this.$team.prepend(this.$teamOptionCustom);
				this.$shift.prepend(this.$shiftOptionCustom);
			}
			
			this.$teamOptionCustom.prop('selected', true);
			this.$shiftOptionCustom.prop('selected', true);
			
			this.prevTeamVal = '0';
			this.prevShiftVal = '0';
		},
		
		unsetOptionCustom: function() {
			if (_.isUndefined(this.screwfix.patterns['0:0'])) {
				this.$teamOptionCustom.remove();
				this.$shiftOptionCustom.remove();
				
				this.$teamOptionCustom = $();
			}
		}
	});
	
	var PatternInputView = Backbone.View.extend({
		
		initialize: function(options) {
			// AppView
			this.master = options.master;
			// FormView
			this.parent = options.parent;
			
			this.patternInputOverviewView = new PatternInputOverviewView({el: this.el, master: this.master, parent: this});
			
			this.patternInputEditView = new PatternInputEditView({el: this.el, master: this.master, parent: this});
			
			this.patterns = options.patterns;
		},
		
		/**
		 * @param {string} id id of pattern eg '2:1'
		 */
		changeOverview: function(id) {			
			if (_.isUndefined(this.screwfix.patterns[id])) {
				// do nothing
				return;
			}
			
			this.patternInputOverviewView.change(this.screwfix.patterns[id]);
		},
		
		/**
		 * Sets pattern input from values of instance of PatternInputEditView
		 * @returns {this}
		 */
		setPatternInput: function() {
			this.patternInputEditView.setPatternInput();
			
			return this;
		}
	});
	
	var PatternInputOverviewView = Backbone.View.extend({
		template: appGlobal.templates.formPatternInputOverview,
		
		initialize: function(options) {
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
		render: function(pattern) {
			var date = this.master.date.clone();
			date.today().startWeek();
			
			var inputPattern = {pattern: pattern, firstDay: date.toString()};
			
			var patternStr = JSON.stringify(inputPattern);
			
			date.setFormat(function(y, m, d, o) {
					return d + ' ' + Zidane.capitalize(o.getMonthString());
			});

			this.$el.html(this.template({pattern: pattern, patternStr: patternStr, date: date}));
			
			this.afterRender();
			
			return this;
		},
		
		afterRender: function() {
			this.delegateEvents();
		},
		
		/**
		 * Changes overview by given pattern.
		 * @param {array} pattern raw unadjusted pattern
		 * @returns {this}
		 */
		change: function(pattern) {
			var adjustedPattern = this.patternDate.adjust(pattern);			
			
			this.render(adjustedPattern);
			
			return this;
		},
		
		customize: function() {
			this.undelegateEvents();
			
			var patternInputValue = this.$el.find("input[name='patternInput']").val();
			
			var pattern = $.parseJSON(patternInputValue).pattern;

			this.parent.patternInputEditView.render(pattern);
		}
	});
	
	var PatternInputEditView = Backbone.View.extend({
		template: appGlobal.templates.formPatternInputEdit,
		
		initialize: function(options) {
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
		
		render: function(pattern) {
			var date = this.master.date.clone();
			date.today().startWeek();
			
			var inputPattern = {pattern: pattern, firstDay: date.toString()};
			
			var patternStr = JSON.stringify(inputPattern);
			
			date.setFormat(function(y, m, d, o) {
					return d + ' ' + Zidane.capitalize(o.getMonthString());
			});
			
			// dates will be filled in the template script
			this.daysData = [];
			
			this.days = [];
			
			this.$el.html(this.template({pattern: pattern, patternStr: patternStr, date: date, view: this}));
			
			var that = this;
			
			this.$el.find("td.day").each(function(i) {
				var day = new PatternDayView({el: this, master: that.master, parent: that, data: that.daysData[i]});
				that.days.push(day);
				that.listenTo(day, 'changed', that.changed);
			});
			
			this.afterRender();			
				
			return this;
		},
		
		afterRender: function() {
			this.isActive = true;
			this.$input = this.$el.find("input[name='patternInput']");
		},
		
		uncustomize: function() {
			this.setPatternInput();
			
			var patternInputValue = this.$input.val();
			
			var pattern = $.parseJSON(patternInputValue).pattern;
			
			this.isActive = false;
			
			this.parent.patternInputOverviewView.render(pattern);
		},
		
		/**
		 * Collects pattern from times kept in pattern day views.
		 * @returns {Array}
		 */
		getPattern: function() {
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
		setPatternInput: function() {
			if (this.isActive) {
				var date = this.days[0].getDate();
				var firstDay = new Zidane.Calendar(date.getYear(), date.getMonth(), date.getDate());
				
				var patternInput = {pattern: this.getPattern(), firstDay: firstDay.toString()};

				this.$input.val(JSON.stringify(patternInput));
			}
			
			return this;
		},
		
		addWeek: function() {
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
		
		removeWeek: function() {
			var pattern = this.getPattern();
			
			if (pattern.length > 1) {
				// removes last week
				pattern.pop();
			}
			
			this.render(pattern);
			
			this.changed();
			
			return this;
		},
		
		changed: function() {
			this.master.formView.patternSelectorView.setOptionCustom();
		}
	});
	
	/**
	 * @trigger changed() when new time is selected or changed state of day view
	 */
	var PatternDayView = Backbone.View.extend({
		templateIn: appGlobal.templates.formPatternInputDayIn,
		templateOff: appGlobal.templates.formPatternInputDayOff,
		
		initialize: function(options) {
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
		
		render: function() {
			if (this.state === 'in') {
				this.renderIn();
			}
			else {
				this.renderOff();
			}
			
			return this;
		},
		
		renderIn: function() {
			this.$el.attr('class', 'day dayIn');
			this.$el.html(this.templateIn({date: this.date, times: this.times, view: this}));
			
			this.$selects = this.$el.find('select');
			
			return this;
		},
		
		renderOff: function() {
			this.$el.attr('class', 'day dayOff');
			this.$el.html(this.templateOff({date: this.date, times: this.times, view: this}));
			
			this.$selects = null;
			
			return this;
		},
		
		changeState: function() {
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
			console.log(this.cid + ': DayView fired changed')
			return this;
		},
		
		/**
		 * Sets times from selects. If off day then it sets times to 'off'
		 * @returns {this}
		 */
		setTimes: function() {
			if (this.$selects) {
				this.times = [];
				this.times[0] = $(this.$selects[0]).val() + ':' + $(this.$selects[1]).val();
				this.times[1] = $(this.$selects[2]).val() + ':' + $(this.$selects[3]).val();
			}
			else {
				this.times = 'off';
			}
			
			this.trigger('changed');
			console.log(this.cid + ': DayView fired changed')
			
			return this;
		},
		
		setDefaultTimes: function() {
			this.times = ['00:00', '00:00'];
			
			return this;
		},
		
		getTimes: function() {
			return this.times;
		},
		
		getDate: function() {
			return this.date;
		},
		
		/**
		 * Helper function for template. It pads time.
		 * @param {integer} timeUnit hours or minutes
		 * @returns {string|integer}
		 */
		padTime: function(timeUnit) {
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
		selectOption: function(t, tString, type) {			
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
	
}(jQuery));
