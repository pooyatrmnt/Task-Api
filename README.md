# Tasks API
A simple API System for a task management application can be built upon and used as an API Framework.\

# Key Aspects

1 - This project uses an MVC design pattern, meaning you can create models and controllers based on your needs while working on your project.\
2 - Since this is an API project and it is restful, all you need to do is to name your public methods in your controller class exactly like the HTTP methods you want to use with the project and API, and also add your controller to the TaskApiWebsite.php file and that's it! with only one line of code, you can add as many resource controllers as you need for your project.\
3 - When defining your controllers you can also define a constructor and get the variables you need when creating the controller in the TaskApiWebsite.php.\
4 - There is also a DatabaseTable class file inside the Ninja namespace that makes managing database interactions easy, also it supports ordering and pagination if needed.\
5 - This project also comes with an autoloader in the functions directory that will autoload any class and file if placed in the root directory and use PSR-4 guides for namespace naming and managing directories. this class will return objects of the Php's default StdClass but you can send it a Custom Model Class by passing the optional arguments className and constructorArgs.\
6 - This project also automatically takes care of 404 and not found routes for your controllers and 405 Method Not Allowed HTTP error that happens when you don't have a method requested by the client in your controller, also when 405 happens API also sends the Allow header as noted inside MDN Devs Guides and other community guidelines with all of the available methods that are inside your project.\
7 - Also sending responses to your client is very easy all you need to do is to return a value from one of your methods in the controller and it will automatically get sent to the client as JSON formatted output but your response has to be in an array or object format.\

# Install

Settings for the application are set inside TaskApiWebsite.php, you need to set the database information for the PDO class, if you prefer you can also make your own PHP class like TaskApiWebsite.php if you need to do this first you must make sure it implements the Website.php interface and also make the necessary changes in the index.php file to make an object of your own class and then send it to the ApiEntryPoint class object.
