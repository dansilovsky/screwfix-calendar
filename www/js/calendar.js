(function($) {
	
	var appGlobal = {
		// stores compiled tamplates
		templates: {
			dayView: _.template($('#dayTemplate').html(), null, {variable: 'mo'}),
			navigatorView: _.template($('#calendarNavigatorTemplate').html(), null, {variable: 'mo'}),
			toolsView: _.template($('#calendarToolsTemplate').html(), null, {variable: 'mo'}),
			monthView: _.template($('#monthTemplate').html(), null, {variable: 'mo'}),
			calendarView: $('#calendarTemplate'),
			holidaysSelectionInfo: _.template($('#holidaysSelectionInfoTemplate').html(), null, {variable: 'data'}),
			addNote: _.template($('#addNoteTemplate').html(), null, {variable: 'data'}),
			editNote: _.template($('#editNoteTemplate').html(), null, {variable: 'data'}),
			addHolidaysForm: _.template($('#addHolidaysFormTemplate').html(), null, {variable: 'data'}),
			cancelHolidaysForm: _.template($('#cancelHolidaysFormTemplate').html(), null, {variable: 'data'})
		}
	};
	
	/**
	 * Manages 
	 * 
	 * @param {object} initial object containing todays date. eg. {year: 2000, month: 1, day: 1}
	 */
	var DateNavigator = function(initial) {
		
		var y = initial.year;
		var m = initial.month || 1;
		var d = initial.day || 1;
		
		var now = new Zidane.Calendar(y, m, d);
		
		var current = now.clone();
		current.startMonth();
		
		var start = current.clone().startWeek();
		var end = current.clone().endMonth().endWeek();
		
		var move;
		
		return {
			/**
			 * Get clone of now
			 * 
			 * @returns {Zidane.Calendar}
			 */
			getNow: function() {
				return now.clone();
			},
			
			/**
			 * Get clone of current display month. 
			 * It's an Zidane.Calendar instance set to first day of given month.
			 * 
			 * @returns {Zidane.Calendar}
			 */
			getCurrentMonth: function() {
				return current.clone();
			},
				
			currentRange: function() {
				return {
					start: start.toString(), 
					end: end.toString(),
					move: move
				};
			},
			
			/**
			 * Returns clone of current start 
			 * 
			 * @returns {Zidane.Calendar}
			 */	
			getCurrentStart: function() {
				return start.clone();
			},
			
			/**
			 * Returns clone of current end 
			 * 
			 * @returns {Zidane.Calendar}
			 */	
			getCurrentEnd: function() {
				return end.clone();
			},
			
			prevMonth: function() {				
				current.prevMonth().startMonth();
				
				start = current.clone().startWeek();
				end = current.clone().endMonth().endWeek();
				
				move = DateNavigator.BACKWARD;
				
				return this;
			},
			
			nextMonth: function() {
				current.nextMonth().startMonth();
				
				start = current.clone().startWeek();
				end = current.clone().endMonth().endWeek();
				
				move = DateNavigator.FORWARD;
				
				return this;
			},
			
			/**
			 * Use this to move date navigator to previous month after calendar has been moved by week.
			 * @returns this
			 */
			prevMonthFromWeek: function() {
				current = start.clone().startMonth();
				
				start = current.clone().startWeek();
				end = current.clone().endMonth().endWeek();
				
				move = DateNavigator.BACKWARD;
				
				return this;
			},
			
			/**
			 * Use this to move date navigator to next month after calendar has been moved by week.
			 * @returns this
			 */
			nextMonthFromWeek: function() {
				current = end.clone().startMonth();
				
				start = current.clone().startWeek();
				end = current.clone().endMonth().endWeek();
				
				move = DateNavigator.FORWARD;
				
				return this;
			},
				
			prevWeek: function() {
				start.prevWeek();
				end.prevWeek();
				current.prevWeek();
				
				move = DateNavigator.BACKWARD;
				
				return this;
			},
				
			nextWeek: function() {
				start.nextWeek();
				end.nextWeek();
				current.nextWeek();
				
				move = DateNavigator.FORWARD;
				
				return this;
			}
		};
	};
	
	_.extend(DateNavigator.prototype, Backbone.Events);
	
	// DateNavigator constats
	DateNavigator.FORWARD = 'forward';
	DateNavigator.BACKWARD = 'backward';
	DateNavigator.JUMP = 'jump';
	
	/**
	 * Manages selecting of cells
	 * @trigger started()             selection started
	 * @trigger added(selection)      cell added to selection
	 * @trigger selected(selection)   selection ended
	 * @param {jQuery} $selector  
	 * @param {array}  cells      array of all cells(jQuery objects) in a grid
	 * @param {hash}   options
	 */
	var Selector = function($selector, cells, options) {
		
		var defaults = {
			showInfo: false,
			// {callback: function, context: anObject} like backbones events
			info: {callback: null, context: null},
			// callback function must return true if cell is selectable, false otherwise
			checkIsSelectable: {callback: null, context: null}
		};
		
		var S = {
			that: this,
			isSelecting: false,
			startCell: null,
			endCell: null,
			$selector: $selector,
			$document: $(document),
			cells: cells,
			selection: [],
			$selectionInfo: null,
			settings: $.extend({}, defaults, options),
			enabled: false
		};
		
		S.$document.mouseup(function(event) {
			// only for left mouse button
			if (S.isSelecting && event.which === 1) {
				S.isSelecting = false;
				
				// selection ended deactivate info box
				if (S.settings.showInfo) {
					S.deactivateInfo();
				}
				
				S.that.trigger('selected', S.selection);
			}
		});
		
		for (var i=0; i<S.cells.length; i++) {			
			S.cells[i].on('leftmousedown', function(cell, event) {
				if (S.enabled) {
					S.that.trigger('started');

					S.isSelecting = true;

					S.startCell = cell.order;
					S.endCell = cell.order;

					S.select();

					// show selection info
					if (S.settings.showInfo) {					
						S.activateInfo(event);
					}
				}
			});
			
			S.cells[i].on('mouseenter', function(cell) {
				if (S.isSelecting) {
					S.endCell = cell.order;
					S.select();
				}
			});
		}
		
		S.select = function() {
			var checkIsSelectable = S.settings.checkIsSelectable.callback;
			var context = S.settings.checkIsSelectable.context;
			
			var check = S.startCell - S.endCell;
			
			S.selection = [];
			
			var tempSelection = [];
			
			// unselect all cells
			S.that.unselect();
			
			if (check === 0) {
				tempSelection.push(S.cells[S.startCell]);
			}
			else if (check < 0) {
				for (var i=S.startCell; i<=S.endCell; i++) {
					tempSelection.push(S.cells[i]);
				}
			}
			else {
				for (var i=S.startCell; i>=S.endCell; i--) {
					tempSelection.push(S.cells[i]);
				}
			}			
			
			if (checkIsSelectable !== null) {
				for (var i=0, l=tempSelection.length; i<l; i++) {
					context = context !== null ? context : this;
					
					if (checkIsSelectable.call(context, i, tempSelection)) {						
						tempSelection[i].select();
					
						S.selection.push(tempSelection[i]);
					}
				}
			}
			else {
				for (var i=0, l=tempSelection.length; i<l; i++) {
					tempSelection[i].select();
					S.selection.push(tempSelection[i]);
				}
			}
			
			if (S.selection.length > 0) {
				// trigger new cell added. !must be triggered afeter all selection is done
				S.that.trigger('added', S.selection);
			}
		};
		
		S.activateInfo = function(event) {
			S.$selectionInfo = Zidane.create('div')
				.attr('id','calendarSelectionInfo')
				.addClass('popupBox')
				.appendTo(S.$selector);			

			if (S.settings.info.callback) {
				// append result of info callback function
				S.that.appendInfo(S.selection);
				S.that.on('added', S.that.appendInfo, S.that.settings);				
			}
			
			var infoArgs = {
				document: {
					height: S.$document.height(),
					width: S.$document.width()
				},
				box: {
					height: function() {return S.$selectionInfo.outerHeight();},
					width: function() {return S.$selectionInfo.outerWidth();}
				}
			};
			
			S.positionInfoBox(infoArgs, event.pageX, event.pageY);

			S.$selector.mousemove(function(event) {
				S.positionInfoBox(infoArgs, event.pageX, event.pageY);
			});
		};
		
		S.deactivateInfo = function() {
			S.$selectionInfo.remove();
			S.$selectionInfo = null;
			S.$selector.unbind('mousemove');
			S.that.off('added', S.that.appendInfo, S.that.settings);
		};
		
		/**
		 * Helper function to position selection Info box
		 * @param {object} args
		 * @param {int} pageX
		 * @param {int} pageY
		 */
		S.positionInfoBox = function(args, pageX, pageY) {
			var offset = 10;
			var newX = pageX;
			var newY = pageY - args.box.height() - offset;
			
			if (newX > args.document.width - args.box.width() - offset) {
				newX = args.document.width - args.box.width() - offset;
			}
			
			newX += offset;
			
			if (newX < offset) {
				newX = offset;
			}
			
			if (newY < offset) {
				newY = offset;
			}
			
			S.$selectionInfo.css({top: newY, left: newX});
		};
		
		/**
		 * Sets settings.showInfo to given argument.
		 * 
		 * @param {bool} show
		 * @returns {this}
		 */
		this.setShowInfo = function(show) {
			S.settings.showInfo = show;
		};
		
		this.appendInfo = function() {
			S.$selectionInfo.empty();
			var callback = S.settings.info.callback;
			var context = S.settings.info.context;
			if (context) {
				// use given context
				S.$selectionInfo.append(callback.call(context, S.selection));
			}
			else {
				// use current context(Selector)
				S.$selectionInfo.append(callback(S.selection));
			}
		};
		
		/**
		 * Unselects all cells
		 * 
		 * @return {Selector} this
		 */
		this.unselect = function() {
			for (var i=0; i<S.cells.length; i++) {
				S.cells[i].unselect();
			}
			
			return this;
		};
		
		this.enable =  function() {
			S.enabled = true;
			return this;
		};
		
		this.disable = function() {
			S.enabled = false;
			return this;
		};
		
		this.clear = function() {
			for (var i=0; i<S.cells.length; i++) {
				if (S.cells[i]) {
					S.cells[i].off();
				}
				S.cells[i] = null;
			}
			S.that.off();
		};
	};	
	
	_.extend(Selector.prototype, Backbone.Events);
	
	/**
	 * Resizer for MonthView
	 * @param {MonthView} view
	 * @param {int} fixedH sum of heights of elements that never change height when window is resized
	 */
	var Resizer = function(view, fixedH) {
		var R = {
			that: this,
			view: view,
			dayViews: view.dayViews,
			$window: $(window),
			docH: $(document).height(),
			previousTableH: -1,
			winH: null,
			cellBorderHs: [],
			// keeps track if rows were toggled
			rowsToggled: []
		};
		
		R.tableH = R.docH - fixedH;
		
		R.previousWinH = R.$window.height();
		
		R.weeksCount = R.dayViews.length/7;		
		
		function resizeCallback(e) {
			e.data.context.resize();
		}
		
		R.$window.on(
			'resize', 
			{context: R.that}, 
			resizeCallback
		);
	
		this.resize = function() {
			var cellH, cellHExcess, diff;

			R.winH = R.$window.height();

			diff = R.winH - R.previousWinH;

			R.tableH += diff;

			cellH = Math.floor(R.tableH/R.weeksCount);
			
			cellHExcess = R.tableH%R.weeksCount;

			if (R.tableH !== R.previousTableH) {
				// resize day views only if height really changed
				for (var i=0, x=1, xMod, rowI=0, innerCellH, cellBorderH=0; i<R.dayViews.length; i++, x++) {
					xMod = x%7;
					
					// distribute the excess					
					innerCellH = cellHExcess > 0 ? cellH + 1 : cellH;
					
					if (xMod === 1) {
						rowI = i/7;
						
						if (_.isUndefined(R.cellBorderHs[rowI])) {
							R.cellBorderHs[rowI] = R.dayViews[i].$el.outerHeight() - R.dayViews[i].$el.height();
						}
					}
					
					// remove cell (td) borders if any
					innerCellH -= R.cellBorderHs[rowI];
					
					R.dayViews[i].resize(innerCellH);
					
					if (xMod === 0) {
						cellHExcess--;
					}
					
					R.rowsToggled[rowI] = false;
				}
			}

			R.previousWinH = R.winH;
			R.previousTableH = R.tableH;
		};
		
		/**		 
		 * Resizes all cells in the same row to the height of the given one in argument.
		 * Only if the required height is greater than heigt of other cells in the same row.
		 * @param {int} orderNumber
		 */
		this.resizeRowUp = function(orderNumber) {
			var row = Math.floor(orderNumber/7);
			var rowHs = [];
			var maxHeight;
			
			for (var i=row*7, end = row*7+7; i<end; i++ ) {
				if (R.dayViews[i].naturalHeight > R.dayViews[i].totalHeight) {
					rowHs.push(R.dayViews[i].naturalHeight);
				}
				else {
					rowHs.push(R.dayViews[i].totalHeight);

				}
			}
			
			maxHeight = _.max(rowHs);			
			
			for (var i=row*7, end = row*7+7; i<end; i++ ) {
				R.dayViews[i].resizeUp(maxHeight);
			}
			
			R.rowsToggled[row] = true;
			
		};
		
		/**
		 * Resizes all cells in the same row to the their natural height.
		 * @param {int} orderNumber cell's order number
		 */
		this.resizeRowDown = function(orderNumber) {
			var row = Math.floor(orderNumber/7);
			
			for (var i=row*7, end = row*7+7; i<end; i++ ) {
				R.dayViews[i].resizeDown();
			}
			
			R.rowsToggled[row] = false;
		};
		
		/**
		 * Determine if the cell's row is toggled.
		 * @param {int} orderNumber cell's order number
		 */
		this.isRowToggled = function(orderNumber) {
			var row = Math.floor(orderNumber/7);			
			
			return R.rowsToggled[row];
		};

		this.clear = function() {
			R.$window.off('resize', null, resizeCallback);
		};	
	};
	
	/**
	 * Manages holidays.
	 * @param {array} holidaysYears
	 */
	var HolidaysManager = function(holidaysYears) {
		
		var H = {
			that: this,
			years: holidaysYears
		};
		
		return {
			/**
			* Determines holiday year from given date
			* @param {string} date eg. '2014-05-08'
			* @returns {int} holiday year
			*/
		       determineHolidayYear: function(date) {
			       var year = parseInt(date.substring(0, 4));

			       if (H.years[year] && date >= H.years[year].from && date <= H.years[year].to) {
				       return year;
			       }
			       else if (H.years[year+1] && date >= H.years[year+1].from && date <= H.years[year+1].to)
			       {
				       return ++year;
			       }
			       else if (H.years[year-1] && date >= H.years[year-1].from && date <= H.years[year-1].to) {
				       return --year;
			       }
			       else {
				       throw 'Holiday year is not defined for given date.';
			       }
		       },
		       
			/**
			 * Buils object and returns object holidays info from selection.
			 * @param {array} selection  array of selected DayView objects
			 * @param {string} mode
			 * @returns {object} selected holidays info or null if selection is empty
			 */
			getSelectionInfo: function(selection, mode) {
				if (!selection.length) {
					return null;
				}
				
				var info = {};
				var mainHolidayYear = this.determineHolidayYear(selection[0].model.id);
				var oldHolidayYear = 0;
				var holidayYear = 0;
				var oneHolidayLength = mode === AppView.MODE_HOLIDAYS ? 1 : 0.5;
				var addHolidays = [];
				var cancelHolidays = [];
				var cancelHolidaysLength = [];
			
				for (var i=0, key=-1, l = selection.length; i<l; i++) {
					holidayYear = this.determineHolidayYear(selection[i].model.id);
					
					if (holidayYear !== oldHolidayYear) {
						oldHolidayYear = holidayYear;
						key++;
						addHolidays[key] = [];
						cancelHolidays[key] = [];
						cancelHolidaysLength[key] = 0;
					}					
					
					if (!selection[i].isDayOff()) {
						addHolidays[key].push(selection[i]);
					}
					else if (selection[i].isHoliday()) {
						cancelHolidays[key].push(selection[i]);			
						cancelHolidaysLength[key] += selection[i].isHalfday() ? 0.5 : 1;
					}
				}
				
				info.isSplit = (addHolidays.length > 1 || cancelHolidays.length > 1);
				info.mode = mode;
				// info main is for holiday year where selection started
				info.main = {
					add: {
						count: addHolidays[0].length,
						length: addHolidays[0].length * oneHolidayLength,
						selection: addHolidays[0],
						first: addHolidays[0][0],
						last: addHolidays[0][addHolidays[0].length-1]
					},
					cancel: {
						count: cancelHolidays[0].length,
						length: cancelHolidaysLength[0],
						selection: cancelHolidays[0],
						first: cancelHolidays[0][0],
						last: cancelHolidays[0][cancelHolidays[0].length-1]
					},				
					credits: H.years[mainHolidayYear].credits,
					year: mainHolidayYear,
					from: H.years[mainHolidayYear].from,
					to: H.years[mainHolidayYear].to
				};
				
				info.main.debits = H.years[mainHolidayYear].debits + info.main.add.length;
				info.main.available = info.main.credits - info.main.debits;				
				
				if (info.isSplit) {
					// if selection ended in different holiday year than it started then set info extra
					var position = selection[0].model.id < selection[selection.length-1].model.id ? 'after' : 'before';
					
					info.extra = {
						add: {
							count: addHolidays[1].length,
							length: addHolidays[1].length * oneHolidayLength,
							selection: addHolidays[1],
							first: addHolidays[1][0],
							last: addHolidays[1][addHolidays[1].length-1]
						},
						cancel: {
							count: cancelHolidays[1].length,
							length: cancelHolidaysLength[1],
							selection: cancelHolidays[1],
							first: cancelHolidays[1][0],
							last: cancelHolidays[1][cancelHolidays[1].length-1]
						},
						credits: H.years[holidayYear].credits,
						year: holidayYear,
						from: H.years[holidayYear].from,
						to: H.years[holidayYear].to,
						// depends on where selection ended if in holiday year before or after main one
						position: position
					};
					
					info.extra.debits = H.years[holidayYear].debits + info.extra.add.length;
					info.extra.available = info.extra.credits - info.extra.debits;
				}
				else {
					info.extra = null;
				}
				
				return info;
			},
			
			/**
			 * Credits back holidays to a given holiday year
			 * @param {int} year
			 * @param {float} count
			 * @returns {float} total debits
			 */
			credit: function(year, count) {
				H.years[year].debits -= count;
				
				return H.years[year].debits;
			},
			
			/**
			 * Debits available holidays from given holiday year
			 * @param {int} year
			 * @param {float} count
			 * @returns {float} total debits
			 */
			debit: function(year, count) {
				H.years[year].debits += count;
				
				return H.years[year].debits;
			},
			
			getCredits: function(year) {
				return H.years[year].credits;
			},
			
			getDebits: function(year) {
				return H.years[year].debits;
			},			
			
			/**
			 * Days available for given holiday year
			 * @param {int} year  holiday year
			 * @returns {int}
			 */
			getAvailable: function(year) {				
				return H.years[year].credits - H.years[year].debits;
			}
		};		
	};
	
	// Navigator model
	var NavigatorModel = Backbone.Model.extend({});
	
	// Day model
	var DayModel = Backbone.Model.extend({		
		urlRoot: window.document.URL + 'api/days/',
		
		/**
		 * 
		 * @param {string} today  date in format yyyy-mm-dd
		 * @returns {bool}
		 */
		isToday: function(today) {
			return today === this.id ? true : false;				
		}
	});

	// Calendar day collection
	var CalendarDayCollection = Backbone.Collection.extend({
		model: DayModel,
		loadRange: {start: null, end: null},
		// number of calls to filterByDateRange(), which is actually numer of moves in calendar
		moveCounter: 0,
			
		initialize: function(options) {
			this.comparator = 'id';
			this.dateHelper = new Zidane.Calendar();
		},
		
		url: function() {
			return window.document.URL + 'api/days';
		},
		
		filterByDateRange: function(range) {
			this.check(range);
			
			this.moveCounter++;
			
			return this.filter(function(model) {
				var start = range.start;
				var end = range.end;
				
				return (model.id >= start && model.id <= end) ? true : false;					
			});
		},
		
		/**
		 * Checks if models between given dates are available. 
		 * If not then builds and adds new simple days to collection 
		 * and then tries to load complete days data from server.
		 * 
		 * @param {object} range 
		 */	
		check: function(range) {
			var move = range.move;
			var missingDays = [];
			var dateRunner = new Zidane.Calendar();
			dateRunner.setFromStr(range.start);
			var loop = 0;
			while (dateRunner.toString() <= range.end) {
				if (_.isUndefined(this.get(dateRunner.toString()))) {
					missingDays.push(dateRunner.toString());
				}
				
				loop++;
				dateRunner.nextDay();
				
				if (loop > 100) {
					throw 'Only 100 loops in while statement allowed.';
				}
			}
			
			// failed check, missing days in this collection were found
			if (missingDays.length > 0) {
				// first item of array missingDays is actually first day of load range
				this.loadRange.start = missingDays[0];
				// last item of array missingDays is actually last day of load range
				this.loadRange.end = missingDays[missingDays.length-1];
				
				// build missing days
				var days = this.buildDays(this.loadRange);
				
				if (move === DateNavigator.FORWARD) {
					this.push(days, {sort: false});
				}
				else if (move ===  DateNavigator.BACKWARD) {
					this.unshift(days, {sort: false});
				}
				else {
					throw 'Unidentified move.';
				}
				
				this.launchPreloader();
				
				this.fetch({
					remove: false,
					data: {from: this.loadRange.start, to: this.loadRange.end},
					success: function(collection) {
						collection.stopPreloader();
					}
				});				
				
				if (this.models.length > 366 && this.moveCounter%3 === 0) {
					// remove excess days in collection when it reaches 366 days
					// do it only every third time
					this.removeExcessDays(move);
				}
			}	
			
		},
		
		/**
		 * Helper function builds array of days for given date range.
		 * @param {object}  range  contains start and end date string
		 * @returns {array}
		 */
		buildDays: function(range) {
			var dateRunner = new Zidane.Calendar();			
			var today = dateRunner.today().toString();
			dateRunner.setFromStr(range.start);			
			var days = [];
			var loop;
			while (dateRunner.toString() <= range.end) {
				days.push({
					"id": dateRunner.toString(),
					"day": dateRunner.getDate(),
					"note": null,
					"sysNote": null,
					"holiday": null,
					"bankHoliday": null,
					"shiftStart": 'NA',
					"shiftEnd": 'NA',
					"year": dateRunner.getYear(),
					"isFirstDayOfWeek": dateRunner.isFirstDayOfWeek(),
					"isLastDayOfWeek": dateRunner.isLastDayOfWeek()
				});
				
				loop++;
				dateRunner.nextDay();
				
				if (loop > 100) {
					throw 'Only 100 loops in while statement allowed';
				}
			}
			
			return days;
		},
		
		/**
		 * Helper function removes excess days from collection
		 * @param {string}  move  the last move of user in calendar forward, backward or jump
		 */		
		removeExcessDays: function(move) {
			var edge, removeDays = [];
			
			if (move === DateNavigator.BACKWARD) {
				edge = this.dateHelper.setFromStr(this.loadRange.start).nextMonth(12).endWeek().toString();
				
				removeDays = this.filter(function(model){
					return model.id > edge;
				});
			}
			else if (move === DateNavigator.FORWARD) {
				edge = this.dateHelper.setFromStr(this.loadRange.end).prevMonth(12).startWeek().toString();
				
				removeDays = this.filter(function(model){
					return model.id < edge;
				});
			}
			
			this.remove(removeDays);			
		},
		
		updateHolidays: function(models) {
			var that = this;
			this.launchPreloader();
			
			var xhr = $.ajax({
				url: that.url(), 
				type: 'PATCH', 
				dataType: 'json',
				data: JSON.stringify(models),
				contentType: 'application/json',
				success: function(data) {
					that.stopPreloader();
					that.add(data, {merge: true});
					that.trigger('holidaysUpdated');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					that.stopPreloader();
					that.ajaxErrorAlert(null, jqXHR, null);
					that.trigger('holidaysUpdateError');
				}
			});
		}
	});
	
	var AppView = Backbone.View.extend({
		el: $('body'),
		urlRoot: window.document.URL,
		
		initialize: function() {
			this.mode = AppView.MODE_NOTES,
			this.user = new Zidane.User(this.screwfix.user, new Zidane.Acl(this.screwfix.acl.roles));
			
			this.holidaysManager = new HolidaysManager(this.screwfix.holidays.years);
			
			this.calendar = new CalendarView({master: this});
			
			this.calendarPlacer = new Zidane.Placer();
		},
		
		layover: function() {
			var $layover = Zidane.create('div', 'layover')
			// you need to stop native "mouseup" event used in Selector to fire Selector's "selected" event
			.mouseup(function(event) {
				event.stopPropagation();
			})			
			.appendTo(this.el);
			
			return $layover;
		}
	});
	
	AppView.MODE_NOTES = 'notes';
	AppView.MODE_HOLIDAYS = 'holidays';
	AppView.MODE_HALFDAY_HOLIDAYS = 'halfdayHolidays';
	
	// Calendar view
	var CalendarView = Backbone.View.extend({
		el: $('#calendar'),
		$calendarBar: null,

		initialize: function(options) {
			// master is AppView
			this.master = options.master;
			
			this.user = this.master.user;
			
			this.holidaysManager = this.master.holidaysManager;
			
			// collection of day models
			this.calendarDayCollection = new CalendarDayCollection(this.screwfix.calendarDaysData, {comparator: false});
			
			// date navigator
			this.dateNavigator = new DateNavigator(this.screwfix.today);
			
			// model navigator
			this.navigatorModel = new NavigatorModel(this.screwfix.today);			
			
			// view navigator
			this.navigatorView = new NavigatorView({model: this.navigatorModel, master: this.master, parent: this});
			
			// view tools
			this.toolsView = new ToolsView({master: this.master, parent: this});
			
			// view month
			this.monthView = new MonthView({collection: this.calendarDayCollection, master: this.master, parent: this});
			
			this.on('change:month:prev', this.dateNavigator.prevMonth, this.dateNavigator);
			this.on('change:month:next', this.dateNavigator.nextMonth, this.dateNavigator);
			this.on('change:month:prev:week', this.dateNavigator.prevMonthFromWeek, this.dateNavigator);			
			this.on('change:month:next:week', this.dateNavigator.nextMonthFromWeek, this.dateNavigator);
			this.on('change:month', this.monthView.changeMonth, this.monthView);
			this.on('change:month', this.navigatorView.changeMonthDate, this.navigatorView);
			
			this.on('change:week:prev', this.dateNavigator.prevWeek, this.dateNavigator);
			this.on('change:week:next', this.dateNavigator.nextWeek, this.dateNavigator);
			this.on('change:week', this.navigatorView.changeWeekDate, this.navigatorView);
			this.on('change:week', this.monthView.changeMonth, this.monthView);
			
			this.toolsView.on(
				'holidayson', 
				function() {
					this.master.mode = AppView.MODE_HOLIDAYS; 
					this.monthView.selector.enable();
				}, 
				this
			);
			this.toolsView.on(
				'switched', 
				function(mode) {
					switch (mode) {
						case AppView.MODE_NOTES:
							this.master.mode = mode;
							this.monthView.selector.disable();
							break;
						case AppView.MODE_HOLIDAYS:
							this.master.mode = mode;
							this.monthView.selector.enable();
							break;
						case AppView.MODE_HALFDAY_HOLIDAYS:
							this.master.mode = mode;
							this.monthView.selector.enable();
							break;
						default:
							this.master.mode = AppView.MODE_NOTES;
							this.monthView.selector.disable();
							throw 'Unknown mode';
					}
				}, 
				this
			);
			
			this.render();
		},
			
		render: function() {
			this.$el.append(appGlobal.templates.calendarView.html());
			
			this.$calendarBar = this.$el.find('#calendarBar');
			
			this.renderNavigator();
			
			if (this.user.isAllowed(Zidane.Acl.MEMBER)) {
				// render only if user role is member or higher
				this.renderTools();
			}
			
			this.renderMonth();
			
			return this;
		},
			
		renderNavigator: function() {			
			this.$calendarBar.append(this.navigatorView.el);
			
			return this;
		},
		
		renderTools: function() {
			this.$calendarBar.append(this.toolsView.el);
		},

		renderMonth: function() {
			this.$el.append(this.monthView.el);			
			this.monthView.resize();
			
			return this;
		},
			
		prevMonth: function(fromWeek) {
			if (!fromWeek) {
				this.trigger('change:month:prev', {dateNavigator: this.dateNavigator});
			}
			else {
				this.trigger('change:month:prev:week', {dateNavigator: this.dateNavigator});
			}
			
			this.trigger('change:month', {dateNavigator: this.dateNavigator});
			
			return this;
		},
			
		nextMonth: function(fromWeek) {
			if (!fromWeek) {
				this.trigger('change:month:next', {dateNavigator: this.dateNavigator});
			}
			else {
				this.trigger('change:month:next:week', {dateNavigator: this.dateNavigator});
			}
			
			this.trigger('change:month', {dateNavigator: this.dateNavigator});
			
			return this;
		},
			
		prevWeek: function() {
			this.trigger('change:week:prev', {dateNavigator: this.dateNavigator});
			this.trigger('change:week', {dateNavigator: this.dateNavigator});
			
			return this;
		},
			
		nextWeek: function() {			
			this.trigger('change:week:next', {dateNavigator: this.dateNavigator});
			this.trigger('change:week', {dateNavigator: this.dateNavigator});
			
			return this;
		},		
		
		holidaysSelectionInfo: function(selection) {
			var info = this.holidaysManager.getSelectionInfo(selection, this.master.mode);
			
			var template = appGlobal.templates.holidaysSelectionInfo;
			
			return template(info);
		}
	});

	// Navigator view
	var NavigatorView = Backbone.View.extend({
		tagName: 'div',
		id: 'calendarNavigator',
		template: appGlobal.templates.navigatorView,
		
		initialize: function(options) {
			// parent view is CalendarView
			this.parent = options.parent;
			
			this.render();
			
			this.$date = this.$el.find('#dateLabel');
			
			this.mode = NavigatorView.MODE_MONTH;
		},
			
		render: function() {
			this.$el.html(this.template(this.model.attributes));

			return this;
		},
		
		events: {
			"click #prevMonth": "prevMonth",
			"click #nextMonth": "nextMonth"
		},
		
		prevMonth: function(e) {
			e.preventDefault();
			
			if (this.mode === NavigatorView.MODE_MONTH) {
				this.parent.prevMonth(false);
			}
			else {
				this.parent.prevMonth(true);
			}
			
			return this;
		},
			
		nextMonth: function(e) {
			e.preventDefault();
			
			if (this.mode === NavigatorView.MODE_MONTH) {
				this.parent.nextMonth(false);
			}
			else {
				this.parent.nextMonth(true);
			}
			
			return this;
		},
		
		changeMonthDate: function(options) {
			this.mode = NavigatorView.MODE_MONTH;
			
			var currMonth = options.dateNavigator.getCurrentMonth();
			var month = Zidane.capitalize(currMonth.getMonthString());
			var year = currMonth.getYear();
			
			this.$date.text(month + ' ' + year);
			
			return this;
		},
		
		changeWeekDate: function(options) {
			this.mode = NavigatorView.MODE_WEEK;
			
			var start = options.dateNavigator.getCurrentStart();
			var startDay = start.getDate();
			var startMonth = Zidane.capitalize(start.getMonthString());
			var startYear = start.getYear();
			
			var end = options.dateNavigator.getCurrentEnd();
			var endDay = end.getDate();
			var endMonth = Zidane.capitalize(end.getMonthString());
			var endYear = end.getYear();
			
			this.$date.text(startDay + ' ' + startMonth + ' ' + startYear + ' - ' + endDay + ' ' + endMonth + ' ' + endYear);
			
			return this;
		}
	});
	
	NavigatorView.MODE_WEEK = 'week';
	NavigatorView.MODE_MONTH = 'month';
	
	var ToolsView = Backbone.View.extend({
		tagName: 'div',
		id: 'calendarTools',
		className: "switcher",
		template: appGlobal.templates.toolsView,
		
		initialize: function(options) {
			// parent is CalendarView
			this.parent = options.parent;
			
			this.render();
		},
		
		events: {
			"click a": "preventDefault"
		},
		
		render: function() {
			var that = this;
			
			this.$el.html(this.template);
			
			this.afterRender();
		},
		
		afterRender: function() {
			var that = this;
			
			this.$el.switcher({
				id: 'mode',
				select: function(el){
					$(el).addClass('on');
				},
				unselect: function(el){
					$(el).removeClass('on');
				},
				switch: function(mode, i) {
					that.trigger('switched', mode);
				}
			});
		},
		
		preventDefault: function(e) {
			e.preventDefault();
		}
	});
	
	var MonthView = Backbone.View.extend({
		tagName: 'div',
		id: 'calendarTableContainer',
		template: appGlobal.templates.monthView,
		
		initialize: function(options) {
			// parent view is CalendarView
			this.parent = options.parent;
			// master is AppView
			this.master = options.master;
			
			this.$window = $(window);
			
			this.$tableMain = null;
			
			this.$el.mousewheel(function(event, delta){
				if (delta > 0) {
					options.parent.prevWeek();
				}
				else {
					options.parent.nextWeek();
				}
			});
			
			this.selectionMode = false;
			
			this.dateNavigator = this.parent.dateNavigator;
			
			this.dayViews = [];
			
			this.resizer = null;
			
			this.selector = null;
			
			this.render(this.dateNavigator);
		},
		
		events: {
			"mousedown table#calendarMainTable": "select"
		},
		
		render: function(dateNavigator) {
			this.clear();
			
			this.$el.html(this.template({}));
			
			this.$tableMain = this.$el.find('table#calendarMainTable');

			var that = this;
			var now = dateNavigator.getNow().toString();
			// current display monht date string yyyy-mm
			var current = dateNavigator.getCurrentMonth().toString().substr(0, 7);
			// order number of cell in display month
			var order = 0;
			var $tr;			
			// use fragment to avoid unnecessary browser DOM reflows viz. http://ozkatz.github.io/avoiding-common-backbonejs-pitfalls.html
			var fragment = document.createDocumentFragment();
			var models = this.collection.filterByDateRange(dateNavigator.currentRange());

			_.each(models, function(item) {
				var dayView = new DayView({
					model: item,
					now: now,
					currDisplayMonth: current,
					master: that.parent.master,
					parent: that,
					resizer: that.resizer,
					order: order++
				});

				that.dayViews.push(dayView);

				if (dayView.isFirstDayOfWeek()) {
					$tr = Zidane.create('tr');
					fragment.appendChild($tr[0]);
				}

				$tr.append(dayView.render().el);
			});
			
			this.$tableMain.append(fragment);
			
			this.afterRender();
			
			return this;
		},
		
		/**
		 * Called after view is rendered
		 */
		afterRender: function() {
			var that = this;
			
			this.selector = new Selector(
				this.$el, 
				this.dayViews, 
				{
					info: {callback: this.parent.holidaysSelectionInfo, context: this.parent}
				}
			);
			
			if (this.master.mode === AppView.MODE_HOLIDAYS || this.master.mode === AppView.MODE_HALFDAY_HOLIDAYS) {
				this.selector.enable();
			}

			this.selector.on('started', function() {
				var mode = that.master.mode;
				if (mode === AppView.MODE_HOLIDAYS || mode === AppView.MODE_HALFDAY_HOLIDAYS) {
					this.setShowInfo(true);
				}
				else {
					this.setShowInfo(false);
				}
			});
			
			this.selector.on(
				'selected', 
				function(selection) {
					var mode = this.master.mode;
					
					if (mode === AppView.MODE_HOLIDAYS || mode === AppView.MODE_HALFDAY_HOLIDAYS && selection.length) {
						new HolidaysFormView({
							master: this.master,
							parent: this,
							selection: selection,
							mode: mode
						});
					}
				}, 
				this
			);
			
			this.resizer = new Resizer(this, this.screwfix.dimensions.fixedHeight);
		},

		resize: function() {
			this.resizer.resize();
			
			return this;
		},
		
		clear: function() {
			for (var i=0; i<this.dayViews.length; i++) {
				this.dayViews[i].clear();
				this.dayViews[i].remove();
				this.dayViews[i] = null;
			}
			
			if (this.$tableMain) {
				this.$tableMain.empty();
				this.$tableMain = null;
			}
			
			this.dayViews = [];
			
			if (this.selector) {
				// remove old events
				this.selector.clear();
				this.selector = null;
			}
			
			if (this.resizer) {
				this.resizer.clear();
				this.resizer = null;
			}
		},
			
		changeMonth: function(options) {
			this.render(options.dateNavigator);
			this.resize();
		},
		
		unselect: function() {
			_.each(this.dayViews, function(element){
				element.unselect();
			});
		}
	});
	
	var DayView = Backbone.View.extend({
		tagName: 'td',
		template: appGlobal.templates.dayView,
		selection: {
			isSelected: true
		},

		initialize: function(options) {
			var that = this;
			
			this.$divSelected = null;
			
			this.$cellWrapper = null;
			
			this.now = options.now;
			this.currDisplayMonth = options.currDisplayMonth;			
			// master is AppView
			this.master = options.master;
			// parent is MonthView
			this.parent = options.parent;
			
			this.resizer = options.resizer;
			
			this.user = options.master.user;
			// order number in display month 
			this.order = options.order;
			// holds all cell bars of this DayView
			this.$cellBars = null;
			// holds less/more link
			this.$lessMoreLink = null;
			// state of show less/more (0 - show nothing, 1 - show more, 2 - show less)
			this.lessMoreState = 0;
			// natural height of cell
			this.naturalHeight = null;
			// height of the whole cell when all cell bars would be displayed
			this.totalHeight = null;
			
			this.cellBarHeight = this.screwfix.dimensions.cellBarHeight;
			
			this.hiddenBars = null;
			
			this.listenTo(this.model, 'change', this.onChange);
			
			this.$el.on('mousedown', function(event){
				if (event.which === 1) {
					that.trigger('leftmousedown', that, event);
				}
			});
			
			this.$el.on('mouseenter', function(event) {
				that.trigger('mouseenter', that, event);
			});
		},
		
		events: {
			"click": "addNote",
			"click .note": "editNote",
			"click .sysNote": "editNote",
			"click a.lessMoreLink": "showLessMore",
			"render": "afterRender"
		},

		render: function() {
			this.$el.html(this.template({data: this.model.attributes, view: this}));
			
			this.$el.trigger('render');
			
			return this;
		},
		
		onChange: function(model, options) {			
			if (this.model.id === model.id) {
				this.render();
				
				if (options.action) {
					if (options.action === 'save') {
						if (this.parent.resizer.isRowToggled(this.order)) {
							this.parent.resizer.resizeRowUp(this.order);
						}
					}
					else {
						if (this.parent.resizer.isRowToggled(this.order)) {
							this.parent.resizer.resizeRowUp(this.order);
						}
					}
				}
			}
		},
		
		/**
		 * Called after view is rendered
		 */
		afterRender: function() {
			var notes = this.model.get('note');
			var sysNotes = this.model.get('sysNote');
			
			this.$divSelected = this.$el.find('div.selected');
			
			this.$cellWrapper = this.$el.children();
			
			this.$cellBars = this.$cellWrapper.children('.cellBar');
			
			this.$lessMoreLink = $(this.$cellBars[0]).find('a');
			
			this.totalHeight = this.$cellBars.length * this.screwfix.dimensions.cellBarHeight;
			
			if (this.naturalHeight !== null){
				this.resize(this.naturalHeight);
			}
			
			if (notes !== null) {
				this.$el.find('.note').each(function(i){
					$(this).data('note', {i: i, type: 'personal', id: notes[i].id, val: notes[i].note});
				});
			}
			
			if (sysNotes !== null) {
				this.$el.find('.sysNote').each(function(i){
					$(this).data('note', {i: i, type: 'system', id:sysNotes[i].id, val: sysNotes[i].note});
				});
			}
		},
		
		/**
		 * Returns string representation of this DayView. (eg. Wednesday, 26 April)
		 * 
		 * @returns {string}
		 */
		toString: function() {
			var format = function() {
				return Zidane.capitalize(this.getWeekDayString())+', '+this.getDate()+' '+Zidane.capitalize(this.getMonthString());
			};
			
			var calendar = new Zidane.Calendar(null, null, null, format);
			calendar.setFromStr(this.model.id);
			
			return calendar.toString();
		},
		
		resize: function(height) {			
			this.naturalHeight = height;
			
			this.$cellWrapper.height(this.naturalHeight);
			
			if (this.naturalHeight < this.totalHeight) {
				this.hide(this.naturalHeight);				
				this.lessMoreState = 1;
			}
			else {
				if (this.$cellBars.length > 2) {
					// only if we have any note bars to unhide
					this.unhideNatural();
				}
				
				this.lessMoreState = 0;	
			}
		},
		
		resizeUp: function(height) {
			if (this.$cellBars.length > 2 && this.naturalHeight < this.totalHeight) {				
				this.unhideAll();
			}
			
			this.$cellWrapper.height(height);
		},
		
		resizeDown: function() {				
			this.hide();
			
			this.$cellWrapper.height(this.naturalHeight);
		},
		
		/**
		 * Hide bars that don't fit into cell's natural height.
		 */
		hide: function() {
			// always keep the last "work" bar therefore reduce by 2
			var top = this.$cellBars.length - 2;
			var bottom = Math.floor(this.naturalHeight/this.cellBarHeight) - 1;
			var count = 0;
			
			for (var i=top; i>=1; i--) {
				if (i>=bottom) {
					count++;
					$(this.$cellBars[i]).css('display', 'none');
				}
				else {
					$(this.$cellBars[i]).css('display', 'block');
				}
			}
			
			if (count) {
				this.$lessMoreLink.text(count + ' more');			
				this.lessMoreState = 1;
			}
			else {				
				this.lessMoreState = 0;
			}
		},
		
		/**
		 * Show all cell bars.
		 */
		unhideAll: function() {
			this.$cellBars.each(function(){
				$(this).css('display', 'block');
			});
			
			this.lessMoreState = 2;
			
			this.$lessMoreLink.text('Show less');
		},
		/**
		 * Call only when all cell bars fits into natural height
		 */
		unhideNatural: function() {
			this.$cellBars.each(function(){
				$(this).css('display', 'block');
			});
			
			this.lessMoreState = 0;
			
			this.$lessMoreLink.text('');
		},
		
		showLessMore: function(e) {
			e.stopPropagation();
			
			if (this.lessMoreState === 1) {
				this.parent.resizer.resizeRowUp(this.order);
			}
			else if (this.lessMoreState === 2) {
				this.parent.resizer.resizeRowDown(this.order);
			}			
		},
		
		/**
		 * Event handler. 
		 * Displays form for adding note and saves it.
		 * @param {jQuery event} e
		 */
		addNote: function(e) {
			var note = {
				when: this.toString(),
				i: null,
				val: null,
				type: 'personal',
				action: 'add'
			};

			this.noteForm(note);			
		},
		
		/**
		 * Event handler
		 * @param {jQuery event} e
		 */
		editNote: function(e) {
			e.stopPropagation();
			
			var defaults = {
				when: this.toString(),
				i: null,
				id: null,
				val: null,
				type: null,
				action: 'edit'
			};				

			var note = $(e.target).data('note');

			note = $.extend({}, defaults, note);				

			this.noteForm(note);
						
		},
		
		noteForm: function(note) {
			if (this.master.mode === AppView.MODE_NOTES && this.user.isAllowed(Zidane.Acl.MEMBER)) {
				if (note.type === 'system' && !this.user.isAllowed(Zidane.Acl.EDITOR)) {
					return;
				}
				new NoteFormView({
					note: note, 
					master: this.master, 
					parent: this, 
					user: this.user
				});
			}
		},
		
		/**
		 * Save note to model
		 * @param {object} note
		 */
		saveNote: function(note) {
			var saveOptions = {patch: true, wait: true, action: 'save', 
				success: function(model, response, options) {
					if (note.action === 'edit') {
						// model does not recognize notes edit as a change. Therefore change event has to be triggerd.
						model.trigger('change', model, options);
					}
				},
				error: function() { 
					// do something when validation fails viz. backbone docs on model.save()				
				}
			};
			
			var oldNotes = note.type === 'personal' ? this.model.get('note') : this.model.get('sysNote');
			var newNote;

			if (note.action === 'add') {
				newNote = {id: null, note: note.val};
			}
			else {
				oldNotes[note.i].note = note.val;
				newNote = oldNotes[note.i];
			}
			
			if (note.type === 'personal')
			{
				if (this.user.isAllowed(Zidane.Acl.MEMBER)) {
					this.model.save({note: newNote}, saveOptions);
				}
			}
			else {
				if (this.user.isAllowed(Zidane.Acl.EDITOR)) {
					this.model.save({sysNote: newNote}, saveOptions);
				}
			}
		},
		
		deleteNote: function(note) {
			var deleteOptions = {patch: true, wait: true, action: 'delete'};
			
			var oldNote = note.type === 'personal' ? this.model.get('note') : this.model.get('sysNote');

			var deleteNote = oldNote[note.i];
			deleteNote.note = null;
			
			if (note.type === 'personal')
			{
				if (this.user.isAllowed(Zidane.Acl.MEMBER)) {
					this.model.save({note: deleteNote}, deleteOptions);
				}
			}
			else {
				if (this.user.isAllowed(Zidane.Acl.EDITOR)) {
					this.model.save({sysNote: deleteNote}, deleteOptions);
				}
			}
		},
		
		addHoliday: function(halfday) {
			var halfday = halfday || 0;
			
			if (this.user.isAllowed(Zidane.Acl.MEMBER)) {
				this.model.save({holiday: halfday}, {patch: true, wait: true});
			}
		},
		
		cancelHoliday: function() {
			if (this.user.isAllowed(Zidane.Acl.MEMBER)) {
				this.model.save({holiday: null}, {patch: true, wait: true});
			}
		},

		isFirstDayOfWeek: function() {
			return this.model.get('isFirstDayOfWeek');
		},

		isLastDayOfWeek: function() {
			return this.model.get('isLastDayOfWeek');
		},
			
		isToday: function() {
			return this.model.id === this.now ? true : false;
		},
		
		isEdge: function() {
			var viewMonth = this.model.id.substr(0, 7);
			
			return viewMonth !== this.currDisplayMonth;
		},
		
		isDayOff: function() {			
			return (
				this.model.get('shiftStart') === 'off' || 
				this.model.get('holiday') !== null || 
				this.model.get('bankHoliday') !== null
			);
		},
		
		isHoliday: function() {
			return (this.model.get('holiday') !== null);
		},
		
		isHalfday: function() {
			var holiday = this.model.get('holiday');
			
			return (holiday !== null && holiday === 1);
		},
		
		select: function() {
			//this.$cell.css('background-color', 'black');
			this.$divSelected.css('display', 'block');
		},
		
		unselect: function() {
			this.$divSelected.css('display', 'none');
		},
		
		placePopup: function($popup) {
			var popupH = $box.height();
			var popupW = $box.width();
			
			var cellH = this.$el.outerHeight();
			var cellW = this.$el.outerWidth();
			
			
			
		},
		
		clear: function() {
			this.$el.off();
		}
	});
	
	var NoteFormView = Backbone.View.extend({
		className: 'popupBox form',		
		addTemplate: appGlobal.templates.addNote,
		editTemplate: appGlobal.templates.editNote,
		
		initialize: function(options) {
			this.master = options.master;
			// parent is DayView
			this.parent = options.parent;
			this.user = options.user;
			this.note = options.note;
			
			this.layover = null;
			
			this.render();
		},
		
		events: {
			"click button[name|='save']": "save",
			"click button[name|='delete']": "delete",
			"click #personal": "switchToPersonal",
			"click #system": "switchToSystem"
		},
		
		render: function() {			
			if (this.note.action === 'add') {
				this.renderAdd();
			}
			else {
				this.renderEdit();
			}
			
			this.layover = new Screwfix.common.LayoverView();
			this.layover.on(
				'click', 
				function() {
					this.parent.unselect();
					this.clear();
				},
				this			
			);
			
			this.$el.appendTo(this.master.el)
			.find('textarea').setCursorPosition();
			
			this.master.calendarPlacer.place(this.$el, this.parent.$el);			
		},
		
		renderAdd: function() {
			$.extend( 
				this.note, 
				{showSwitcher: this.user.isAllowed(Zidane.Acl.EDITOR)}
			);
			
			this.$el.html(this.addTemplate(this.note));
						
		},
		
		renderEdit: function() {
			if (this.note.type === 'system' && !this.user.isAllowed(Zidane.Acl.EDITOR)) {
				this.remove();
				return;
			}
			
			this.$el.html(this.editTemplate(this.note));
		},
		
		switchToPersonal: function(e) {
			this.switch('personal', e);
		},
		
		switchToSystem: function(e) {			
			this.switch('system', e);
		},
		
		switch: function(type, e) {
			this.note.type = type;
			
			$(e.target)
			.closest('div.switcher')
			.find('a')
			.each(function(){
				var $el = $(this);
			
				$el.removeClass();
				
				if ($el.attr('id') === type) {
					$el.addClass('on');
				}
			});
		},
		
		save: function() {
			var val = this.$el.find('textarea').val().trim();
			
			if (val === '' || val === this.note.val) {
				this.clear();
				return;
			}
			
			this.note.val = val;
			
			this.parent.saveNote(this.note);
			
			this.clear();			
		},
		
		delete: function() {
			this.parent.deleteNote(this.note);
			
			this.clear();
		},
		
		clear: function() {
			if (this.layover) {
				this.layover.remove();
			}
			
			this.remove();
		}
		
		
	});
	
	var HolidaysFormView = Backbone.View.extend({
		className: 'popupBox form',
		addTemplate: appGlobal.templates.addHolidaysForm,
		cancelTemplate: appGlobal.templates.cancelHolidaysForm,
		
		initialize: function(options) {
			this.master = options.master;
			// MonthView
			this.parent = options.parent;
			this.selection = options.selection;
			this.mode = options.mode;
			this.holidaysManager = this.master.holidaysManager;
			this.user = this.master.user;
			// CalendarDayCollection
			this.collection = this.selection[0].model.collection;
			this.layover = null;
			
			this.info = this.holidaysManager.getSelectionInfo(this.selection, this.mode);
			
			this.isMainAddAction = (this.info.main.add.count > 0 && this.info.main.available >= 0);
			this.isExtraAddAction = (this.info.extra && this.info.extra.add.count > 0 && this.info.extra.available >= 0) ? true : false;
			this.isAddAction = ((this.isMainAddAction) || (this.isExtraAddAction));
			this.isCancelAction = (this.info.main.cancel.count > 0 || (this.info.extra && this.info.extra.cancel.count > 0) && !this.isAddAction);
			this.isAddHalfday = this.mode === AppView.MODE_HOLIDAYS ? false : true;
			
			this.allowClearCollectionEvents = true;	
			
			this.info.tHolidaysYear = function(from, to) {return from.substr(0, 4) + '-' + to.substr(2, 2);};
			this.info.tIsMainAddAction = this.isMainAddAction;
			this.info.tIsExtraAddAction = this.isExtraAddAction;
			
			this.collection.on('holidaysUpdated', this.updateHolidaysManager, this);
			this.collection.on('holidaysUpdateError', this.updateError, this);		
			
			this.render();
		},
		
		events: {
			"click button[name|='addOrCancel']": "addOrCancel"
		},
		
		render: function() {
			if (this.isAddAction) {
				// if there is at least one holiday to be added in a selection
				this.$el.html(this.addTemplate(this.info));
			}
			else if (this.isCancelAction) {
				this.$el.html(this.cancelTemplate(this.info));
			}
			else {
				this.clear();
				return;
			}
			
			this.layover = new Screwfix.common.LayoverView();
			this.layover.on(
				'click', 
				function() {
					this.clear();
				},
				this			
			);
		
			this.master.$el.append(this.el);
			
			this.master.calendarPlacer.place(this.$el, this.selection[this.selection.length-1].$el);
		},
		
		addOrCancel: function() {
			var joinSelection = [];	
			
			if (this.isAddAction) {
				if (this.isMainAddAction) {
					joinSelection = this.info.main.add.selection;
				}
				
				if (this.isExtraAddAction) {
					joinSelection = joinSelection.concat(this.info.extra.add.selection);
				}
				
				this.add(joinSelection);
			}
			else if (this.isCancelAction) {
				if (this.info.main.cancel.count > 0) {
					joinSelection = this.info.main.cancel.selection;
				}
				
				if (this.info.extra && this.info.extra.cancel.count > 0) {
					joinSelection = joinSelection.concat(this.info.extra.cancel.selection);
				}
				
				this.cancel(joinSelection);
			}
			
			this.allowClearCollectionEvents = false;
			
			this.clear();
		},
		
		/**
		 * Adds holidays for given days
		 * @param {array} addHolidays  array of DayViews
		 */
		add: function(addHolidays) {
			var halfday = this.isAddHalfday ? 1 : 0;
			var models = [];
			
			for (var i=0, l=addHolidays.length; i<l; i++) {
				models.push({id: addHolidays[i].model.id, holiday: halfday});
			}
			
			this.collection.updateHolidays(models);			
		},
		
		/**
		 * Cancel holidays for given days
		 * @param {array} cancelHolidays  array of DayViews
		 */
		cancel: function(cancelHolidays) {
			var models = [];
			
			for (var i=0, l=cancelHolidays.length; i<l; i++) {
				models.push({id: cancelHolidays[i].model.id, holiday: null});
			}
			
			this.collection.updateHolidays(models);
		},
		
		updateHolidaysManager: function() {
			if (this.isAddAction) {
				if (this.isMainAddAction) {
					if (!this.isAddHalfday) {
						this.holidaysManager.debit(this.info.main.year, this.info.main.add.length);
					}
					else {
						this.holidaysManager.debit(this.info.main.year, this.info.main.add.length);
					}
				}
				
				if (this.isExtraAddAction) {
					if (!this.isAddHalfday) {
						this.holidaysManager.debit(this.info.extra.year, this.info.extra.add.length);
					}
					else {
						this.holidaysManager.debit(this.info.extra.year, this.info.extra.add.length);
					}
				}
			}
			else if (this.isCancelAction) {
				if (this.info.main.cancel.count > 0) {
					this.holidaysManager.credit(this.info.main.year, this.info.main.cancel.length);
				}
				
				if (this.info.extra && this.info.extra.cancel.count > 0) {
					this.holidaysManager.credit(this.info.extra.year, this.info.extra.cancel.length);
				}
			}
			
			this.allowClearCollectionEvents = true;
			
			this.clear();
		},
		
		updateError: function() {
			this.allowClearCollectionEvents = true;
			
			this.clear();
		},
		
		clear: function() {
			if (this.layover) {
				this.layover.remove();
			}
			
			this.parent.unselect();
			
			if(this.allowClearCollectionEvents) {
				this.collection.off('holidaysUpdated', this.updateHolidaysManager, this);
				this.collection.off('holidaysUpdateError', this.updateError, this);
			}
			
			this.remove();
		}
	});

	//create instance of master view
	var app = new AppView();

}(jQuery));


