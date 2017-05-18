/**
 * class voyc.Model
 * @param {Object=} observer
 * @constructor
 * A singleton object
 */
voyc.Model = function () {
	if (voyc.Model._instance) return voyc.Model._instance;
	voyc.Model._instance = this;
	this.setup();
}

voyc.Model.prototype.setup = function () {
	this.observer = new voyc.Observer();
	new voyc.View(this.observer);
	new voyc.User(this.observer);
	new voyc.Account(this.observer);
	new voyc.AccountView(this.observer);

	// set drawPage method as the callback in BrowserHistory object
	var self = this;
	new voyc.BrowserHistory('name', function(pageid) {
		var event = pageid.split('-')[0];
		self.observer.publish(new voyc.Note(event+'-requested', 'model', {page:pageid}));
	});

	// server communications
	var url = '/svc/';
	if (window.location.origin == 'file://') {
		url = 'http://model.hagstrand.com/svc';  // for local testing
	}
	this.comm = new voyc.Comm(url, 'acomm', 2, true);

	// attach app events
	var self = this;
	this.observer.subscribe('profile-requested'   ,'model' ,function(note) { self.onProfileRequested    (note); });
	this.observer.subscribe('profile-submitted'   ,'model' ,function(note) { self.onProfileSubmitted    (note); });
	this.observer.subscribe('setprofile-posted'   ,'model' ,function(note) { self.onSetProfilePosted    (note); });
	this.observer.subscribe('setprofile-received' ,'model' ,function(note) { self.onSetProfileReceived  (note); });
	this.observer.subscribe('getprofile-received' ,'model' ,function(note) { self.onGetProfileReceived  (note); });

	this.observer.publish(new voyc.Note('setup-complete', 'model', {}));
	//(new voyc.3).nav('home');
}

voyc.Model.prototype.onProfileRequested = function(note) {
	var svcname = 'getprofile';
	var data = {};
	data['si'] = voyc.getSessionId();
	
	// call svc
	var self = this;
	this.comm.request(svcname, data, function(ok, response, xhr) {
		if (!ok) {
			response = { 'status':'system-error'};
		}
		self.observer.publish(new voyc.Note('getprofile-received', 'model', response));
	});
	this.observer.publish(new voyc.Note('getprofile-posted', 'model', {}));
}

voyc.Model.prototype.onGetProfileReceived = function(note) {
	var response = note.payload;
	if (response['status'] == 'ok') {
		console.log('getprofile success');
		voyc.$('gender').value = response['gender'];
		voyc.$('photo' ).value = response['photo' ];
		voyc.$('phone' ).value = response['phone' ];
	}
	else {
		console.log('getprofile failed');
	}
}

voyc.Model.prototype.onProfileSubmitted = function(note) {
	var svcname = 'setprofile';
	var inputs = note.payload.inputs;

	// build data array of name/value pairs from user input
	var data = {};
	data['si'] = voyc.getSessionId();
	data['gender'] = inputs['gender'].value;
	data['photo' ] = inputs['photo' ].value;
	data['phone' ] = inputs['phone' ].value;
	
	// call svc
	var self = this;
	this.comm.request(svcname, data, function(ok, response, xhr) {
		if (!ok) {
			response = { 'status':'system-error'};
		}

		self.observer.publish(new voyc.Note('setprofile-received', 'model', response));

		if (response['status'] == 'ok') {
			console.log('setprofile success' + response['message']);
		}
		else {
			console.log('setprofile failed');
		}
	});

	this.observer.publish(new voyc.Note('setprofile-posted', 'model', {}));
}

voyc.Model.prototype.onSetProfilePosted = function(note) {
	console.log('setprofile posted');
}

voyc.Model.prototype.onSetProfileReceived = function(note) {
	console.log('setprofile received');
}

/* on startup */
window.addEventListener('load', function(evt) {
	voyc.model = new voyc.Model();
}, false);
