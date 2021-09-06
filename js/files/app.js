const cookieParser = require('cookie-parser');
const session = require('express-session');
const sqlite3 = require('better-sqlite3');
const bodyParser = require('body-parser');
const nunjucks = require('nunjucks');
const express = require('express');
const { SHA3 } = require('sha3');

let app = express();

// Configure URL for static content
app.use('/static', express.static('static'))

// Configure JSON and Cookie parser
app.use(bodyParser.urlencoded({extended : true}));
app.use(bodyParser.json());
app.use(cookieParser());

let db = new sqlite3('./static/vendor/db.sqlite3');

// Configure session management for express
app.use(session({
    secret: "s3cr3tk3y",
	resave: true,
	saveUninitialized: true,
    cookie: { secure: false }
}));

// Configure template folder for rendering engine
nunjucks.configure('templates', {
    autoescape: true,
    express: app
});

// Function for GET Request to main page
app.get('/', function (req, res) {
    // Parse error messages to show from Cookie entries
    var messages = "";
    if (req.cookies && req.cookies['messages']) {
        messages = req.cookies['messages'];
        res.clearCookie('messages');
    }
    // Is the current request is authenticated (user is logged in)
    if (req.session && req.session.username && req.session.loggedIn) {
        // Get all Notes from the Database
        let notes = db.prepare("SELECT * FROM notes WHERE user=?").all([req.session.username]);
        // Beautify timestamps of the Notes
        for (let i = 0; i < notes.length; i++)
            notes[i].created = (new Date(notes[i].created)).toString().substr(0, 24);
        // Render those Notes into the content.html template
        res.render('content.html', {notes: notes, messages: messages});
    } else {
        // User is not logged in, thus render login / register page
        res.render('login.html', {messages: messages});
    }
});

// Function for GET Request to the about page
app.get('/about', function (req, res) {
    // Is the current request is authenticated (user is logged in)
    if (req.session && req.session.username && req.session.loggedIn) {
        // Render about page
        res.render('about.html');
    } else {
        // User is not logged in, thus redirect to main page
        res.redirect('/');
    }
});

// Function for GET Request to the logout endpoint
app.get('/logout', function (req, res) {
    // Clear session information
    req.session.loggedIn = false;
    req.session.username = '';
    req.session.destroy();
    // Redirect to main page
    res.redirect('/');
});

// Function for hashing passwords
function pwHash(password) {
    let hash = new SHA3(512);
    hash.update(password);
    return hash.digest('hex');
}

// Function for POST Request to the login endpoint
app.post('/login', function (req, res) {
    var messages = "";
    // Check if all parameters are there
    if (!req.body || !req.body.username || !req.body.password) {
        messages = "Missing Parameters!";
        res.status(400);
    } else {
        // Validate if credentials are correct and that user exists
        let credentials = [req.body.username, pwHash(req.body.password)];
        let user = db.prepare("SELECT * FROM users WHERE username=? AND password=?").get(credentials);
        // If login failed
        if (!user) {
            messages = "Invalid credentials!";
            res.status(400);
        } else {
            // Create session (login user) and return success
            req.session.loggedIn = true;
            req.session.username = req.body.username;
            res.status(200);
        }
    }
    res.cookie('messages', messages);
    res.send('');
});

// Function for POST Request to the registration endpoint
app.post('/register', function (req, res) {
    var messages = "";
    // Check if all parameters are there
    if (!req.body || !req.body.username || !req.body.password) {
        messages = "Missing Parameters!";
        res.status(400);
    } else {
        // Validate if user already exists
        let user = db.prepare("SELECT * FROM users WHERE username=?").get([req.body.username]);
        if (user) {
            messages = "User Already Exists!";
            res.status(400);
        } else {
            // If not create user and session (log in user)
            let data = [req.body.username, pwHash(req.body.password)];
            db.prepare("INSERT INTO users (username, password) VALUES (?, ?);").run(data);
            req.session.loggedIn = true;
            req.session.username = req.body.username;
            res.status(200);
        }
    }
    res.cookie('messages', messages);
    res.send('');
});

// Function for POST Request to the create endpoint for notes
app.post('/create', function (req, res) {
    var messages = "";
    // Is the current request is authenticated (user is logged in)
    if (req.session && req.session.username && req.session.loggedIn) {
        // Check if all parameters are there
        if (!req.body || !req.body.head || !req.body.content) {
            messages = "Missing Parameters!";
            res.status(400);
        } else {
            // If all is correct, create the Note and save it to DB
            let now = new Date().getTime();
            let data = [req.session.username, req.body.head, req.body.content, now];
            db.prepare("INSERT INTO notes VALUES (?, ?, ?, ?);").run(data);
            res.status(200);
        }
    } else {
        messages = "You are not logged in!"
        res.status(400);
    }
    res.cookie('messages', messages);
    res.send('');
});

// Start express
const port = 8000;
app.listen(port, () => console.log(`Example app listening on port ${port}!`))