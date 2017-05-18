# accounts
User Authentication

A library of AJAX services:

  * register
  * login
  * logout
  * reset_password
  * forgot_password
  * change_username
  * change_email
  * change_password

A demo/test AJAX application is also provided.

Javascript is required.  Cookies are not required.

## Installation

How to install the accounts project as a stand-alone application.

  1. Clone the accounts project onto your docroot path.
  1. Download the files for the four submodules.
  1. Launch index.html in your browser.
  
<pre>
   /* Download the source files. */
   git clone http://git.hagstrand.com/accounts.git
   cd accounts
   git submodule update --init --recursive
 </pre>	

## Implementation

How to embed the accounts project into your own project.

  1. Add the Accounts project as a submodule into your project's html folder.
  
  1. If you haven't already, also add the four required submodules: normalize.css, minimal, iicon, and jslib.

<pre>
   /* Add Download the source files. */
   cd myproject/html
   git submodule add http://git.hagstrand.com/accounts.git

   /* Add four required submodules. */
   git submodule add https://github.com/necolas/normalize.css
   git submodule add http://github.com/hagstrand/minimal.git
   git submodule add http://github.com/hagstrand/icon.git
   git submodule add http://github.com/hagstrand/jslib.git
   cd ../../

   /* Download the source files for all the submodules. */
   git submodule update --init --recursive
</pre>	

## Design Principles

  1. This project is open source.  The source code for this library is provided publicly on github so it can be vetted by multiple experts, and so the security algorithms do not depend on obfuscation and are not susceptible to reverse-engineering.
  
  1. Sensitive information including seeds and database credentials is placed in the config file, and the config file is not included in the git repository and is not on the docroot path.

  1. The directory structure is organized so the server-side PHP code is not exposed on the docroot path.

  1. A second-stage email verification is required after register, change-password, change-username, change-email, and reset-password requests.

  1. It is recommended that anonymous users be allowed to use the application, but database saves are executed only for verified users.

  1. For PHP server configuration security, we rely on our hosting service.

  1. We do NOT use PHP sessions.  Instead we create our own session-id "token".

  1. We DO use PHP password_hash() and password_verify() with the default parameters.  Algorithm and random-generated salt are concatenated to the hash in the returned value.

  1. All database access is done with prepared statements.

  1. All POST inputs to services are filtered on the server immediately.

  1. Error codes from the services contain minimal information.

  1. All http requests use POST method, not GET.

  1. It is recommended that all http requests are made to an SSL server with a valid certificate, though this is recommended but not enforced by this application.
  
  1. CORS is implemented so that service requests from a non-SSL server can be serviced by this app running on an SSL server.

  1. Tokens do not contain unhashed database keys.

  1. Tokens are stored in local storage instead of cookies, so they are not carried in an http header of every server request.

  1. We enforce
    . valid email address: includes @ and .
    . valid password: between 4 and 64 chars
	. valid username: between 4 and 64 chars
  
  1. User's private email address is obscured when returned to the client on login.
  
## Summary of Threats and Mitigation

**Threat:** SQL injection.<br/>
**Mitigation:** All user inputs are filtered on the server before use.  All SQL is executed with prepared statements.

**Threat:** Password cracking, using databases of stolen password hashes.<br/>
**Mitigation:** A seed is concatenated with user's password.

**Threat:** Password guessing.<br/>
**Mitigation:** Detect, limit, and log failed attempts.

**Threat:** Password probing attack can become a DOS attack unintentionally.<br/>
**Mitigation:** ?

**Threat:** Leaking server setup details.<br/>
**Mitigation:** Limited error codes returned to client.  Details of errors written to server log, not to client.

**Threat:** Timing attacks.  Example: login attempt with existing username takes longer to respond.<br/>
**Mitigation:** We query for username/password combination with a single query.

**Threat:** Session Hijacking
**Mitigation:** 
  1. Session-id is replaced on any svc call after n minutes.
  1. Session-Id times out after n minutes of inactivity.
  1. Session-Id times out after n minutes since login.

## Unsupported Features:
The following features are NOT supported.

  1. Remember me.  Save username, and fill in the login form next time.
  1. Stay signed in.  Save the session-id or equivalent between sessions.
  1. Unrecognized computer.  Require email verification if user logs in with previously unused ip/user-agent.
  1. Multiple simultaneous sessions.  User logs in on different computers or browsers.
  1. Auto logout.  Use HTTP Push to log the user out and erase the screen on timeout.
		
## Categories
Security,
Privacy,
User Authentication,
Code Hiding,
ID Theft

## How to Install

  1. clone the repository.
	Draw a picture of the directory structure showing:
		where to run the clone
		where to put the config.php file
		where to point the docroot
		
	Sample unix statements.
		mkdir myhost
		cd myhost
		git clone

  1. Create the database
		run create_accounts_schema.sql

  1. Create the config.php file by copying it from config.php sample.
		cp accounts/config.php.sample ../config.php

  1. Modify the config.php file with credentials for your database and system.
     Note: Do NOT allow the modified config.php file to get into the repository.

  1. Run the sql grant statements found in the config.php file, modified with your dbuser name.

## History

Created the Flash project and wrote user management into it.

Created the Login project and copied the user management code from Flash.  Anticipating that this project would be used by Flash, Guru, Voyc, and other projects.  Either embedded into each project, or as a stand-alone single-signon app.

Created the Secure project and copied the code from Login.  Finished the PHP services.  Wrote a unit test program now named svctest.  Started working on a UI program to call the services.

April 2016 - Created the Accounts project and copied the code from Secure.  Put in git repository.  


The Flash project has three entry points: index, flash, and bahasa.

UI concept
The User object does his own login etc.
The User object resides in each app.  Each app may extend it differently.
Should we do a simple Accounts demo, or go all in with Minimal, and create something that can drop into Flash and other apps?  Because we are looking at the User Object.  I think it needs to use the Observer so it is loosely coupled to any other app.

How to use Accounts in other projects.
	Server code.
	Client UI code.
	Client app code, especially the User Object.
Options.
	1. Let each app fork its own version of Accounts and include that as a submodule.
	2. Include Accounts as a submodule.  Use Inheritance to add app logic to the User Object.
	3. Use two User objects, one with UA logic, and one with app logic.
What about the UI?
	Accounts can provide a string of HTML to add an offscreen div.
	Accounts can use Minimal, so the UI will work as expected by the app that is also using Minimal.

Accounts has to be included as two submodules.  One under HTML.  One under phplib.
The svc url will be 
	flash.hagstrand.com/accounts/svc/login 
	flash.hagstrand.com/accounts/svc/ua  where ua is consolidated service.
	flash.hagstrand.com/accounts/svc  where ua is consolidated service.
We're doing a POST, so don't confuse that with adding URL parameters.
Usually svc is a folder.  Do we want to confuse the convention by making it a php file in this case?
If that's an issue, then just use a diffent name.
Make svc a consolidated service in all apps.  No svc folders.


## TODO
  * The create_accounts_schema.sql file exposes a database username.  Can this be hidden?  This file is for postgres and would have to be modified for use with mysql.  We can assume this file will be modified by the user.  So it's a possibility it could accidentally get committed to the repository after the user has modified it.  Remove the grants from this file, and add them as comments to config.php, near the db credentials section.
  * add a config.php.sample file and add it to .gitignore 
  * publish valid username, email, and password requirements here
  * consolidate all svcs into one
  * replace cookie with local storage
  * add remove user (by admin or logged-in user)
  * add block user (by admin)
  * add suspend user (by admin)
  * add svc reverify to resend TIC 

## Notes on php_login_advanced

We took several concepts from project php_login_advanced on github.

Two different techniques for generating a unique key used in project php_login_advanced.  Which one is best?  Which one did we choose and why?

  1. hash('sha256', mt_rand());<br/>
		For rememberme cookie.  Length 64.
			
  1. sha1(uniqid(mt_rand(), true));<br/>
		For email verification for register and reset-password.  Length 40.

## Notes on Email verification

There seem to be two methods of email verification in use on websites.

Method 1.
	Like Chase Bank.
	User has already entered email/password.
	Now in the Verify dialog, he enters Temporary Identification Code and password.
	Pros:
		all handled in one browser window
	Cons:
		user may have to wait for email
		user has to cut and paste between windows
	Mitigation:
		let the user continue working while waiting for the email
		keep the code small and easier to re-type if the user cannot do cut-and-paste
	UI:
		verify dialog: takes TIC and password, btns: Verify/Later
		link to verify dialog from user-options dropdown
		prompt 'thank you' after verify complete
		check with every putcards, whether access-level has changed
	
Method 2.
	Click link in email.
	Nota Bene:
		Hash code in email must be more secure, because email/password are not required.
		The hash takes us straight to the user record, just like a cookie.
	Pros:
		easy for user to click the link
	Cons:
		user can easily get two windows open simultaneously
	Mitigation:
		make sure simultaneous open windows does not break anything
		give him his "success" message in a stripped-down window 
			that encourages him to return to his original window
	UI:
		Thank you page has link to redirect to index.html
