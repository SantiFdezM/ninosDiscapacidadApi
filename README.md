# REST API to help kids with disabilities
This project was made in order to provide an useful tool to medical developers and game developers so that they can focus in helping kids with mental disabilities. Medical developers or any developer interested in the REST API can develop a tool (web, iOS, android, etc) that can consume data from the API that is feed from games. The main idea of this tool it would be that they read the patients, doctors and parents, and design an intranet where parents and doctors can see the kid progress on the games the doctor recommended.

Games can be developed with this API, they just need a token from the API in order to be able to POST data. They can post custom metrics with a measure value represented as a string. This value can be an score, time, tries, etc. This makes the API flexible because a lot of different games can be developed at top of it and with a lot of metrics can help the kids to improve.

### How to setup and install the platform for development

####Install XAMPP or setup manually LAMP in your computer
Use this [guide](https://www.apachefriends.org/index.html) to install XAMPP
Here you can find some [guides](https://www.linode.com/docs/websites/lamp/) on how to insall LAMP

####Run and develope
To run and develope for the REST API you need to do the following:
- fork the repo and clone it in your desktop (In the folder where you have your localhost setup to read)
- create a database
- import `ninos_api.sql` (found in the repo) into the database
- edit `config.php` to setup your local preferences to connect to the database (to test your connection there is a request available to the rest api just call the function `testConnection`)
- run apache and mysql servers
- develope and have fun

### How use the platform

For detailed use of the platform checkout this [guide](https://github.com/SantiFdezM/ninosDiscapacidadApi/blob/master/RestApiManual.pdf)

### Current version

> Version 1.0.0