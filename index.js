const express = require('express');
const app = express();
const handlebars = require('express-handlebars')
const bodyParser = require('body-parser')
// const exec = require('child_process').exec
const session = require('express-session')
const flash = require('connect-flash')
const fs = require('fs')

// Config
  // session
    app.use(session({
      secret: "zqyJT7aaQvjJq9UKGAETc286VePf4HTjU3Zacjpsgsn6Vnaz7hrbuYhSghLcxKRc",
      resave: true,
      saveUninitialized: true
    }))
  // Template Engine
    app.use(express.static(__dirname + '/public'));
    
    app.engine('handlebars', handlebars({defaultLayout: 'main'}))
    app.set('view engine', 'handlebars')
  // Body parser
    //app.use(bodyParser.urlencoded({limit: '150mb', extended: true, parameterLimit:50000}))
    //app.use(bodyParser.json({limit: '150mb'}))
    app.use(express.json({limit: '50mb', extended: true}));
    app.use(express.urlencoded({limit: "50mb", extended: true, parameterLimit:50000})); 
    //app.use(bodyParser.json({limit: '200mb'}));
    //app.use(bodyParser.urlencoded({limit: '200mb', extended: true}));
    //app.use(bodyParser.text({ limit: '200mb' }));
 // Rotas
    app.get('/', function(req, res) {
      console.log(req.session.id)
      res.render('index')
    })
    
    var execPHP = require(__dirname + '/public/js/execphp.js');

    execPHP.phpFolder = '/home/wellington/public_html/nodejsApp/public/php';

    app.use('*.php',function(request,response,next) {
	execPHP.parseFile(request.originalUrl,function(phpResult) {
		response.write(phpResult);
		response.end();
	});
    });

    app.post('/sunpling', function(req, res){
      async function start() {
        var result = await execShellCommand('"' + __dirname + '/public/software/sunplin" ' +   __dirname + '/public/software/input/input_' + req.session.id + '.txt');
        console.log('Sunplin finish [' + req.session + ']: ' + result);
        res.send('{"success" : "Sunplin successfully", "status" : 200, "result": ' + result + '}');
      }

      start();

      console.log('Runing [' + req.session.id + ']')
    })
    
    app.post('/run',  function(req, res){
      console.log('Receive requisition [' + req.session.id + ']')

      var str_input = req.body.params_hash + '#' + req.session.id + '#' + __dirname + "/public/software/results/";

      fs.writeFile( __dirname + '/public/software/input/input_' + req.session.id + '.txt', str_input, function (err) {
        if (err) return console.log(err);
      });

      res.render('status')
    })

    app.get('/download/:file/:ext', function(req, res){
      const file = __dirname + '/public/software/results/' + req.params.file + "_" + req.session.id + "." + req.params.ext;
      var name_file = 'sunplin-distance-mat.';
      if (req.params.file == 'trees'){
        name_file = 'sunplin-trees.';
      }
      name_file = name_file + req.params.ext;
      res.download(file, name_file);
    });

  var server = app.listen(8082)
  server.timeout = 60000 * 60; //(1m * 60m = 1h) 

  console.log("Server running on port: " + server.address().port)

  function execShellCommand(cmd) {
   const exec = require('child_process').exec;
   return new Promise((resolve, reject) => {
    exec(cmd, (error, stdout, stderr) => {
     if (error) {
      console.warn(error);
     }
     resolve(stdout? stdout : stderr);
    });
   });
  }
