### How to use the website :

- First you need to go to the command file and execute all the command at the bottom of the document
- After that, you need to go on the website and click on the profile icon on the top right
  - Then you have to create an account, if you are the first user of the website, you will get the ROLE_PRESIDENT
- You can click on the navbar at top right to access to the different menu linked with your role
- You need to have the ROLE_ADMIN or ROLE_PRESIDENT to access to everything
- As a President, you can remove, add or edit a product, a post, a client or an order

### Documentation : 

- We used Symfony so everything is displayed in different package*
- You have to go to the package "Website" to access to all the file
- In the .env file, you can set the adress of the database and the step of the app (dev or prod)
- The test are done in the "tests" package they will be conducted with PHPUnit
  - in order to make sure of the sanity and quality of the app you should run tests
  - there are two packages for testing "Unit" and 'Func':
    - the Unit package is there to conduct unit tests in order to test small parts of the project.
    - The Func package is there to conduct functionnal tests, they concern larger parts of your project such as
      - Integration tests
      - Application tests where you test user behavior, those tests are most the time conducted on controllers.
  - for more information on testing check PHPUnit documentation [here](https://symfony.com/doc/current/testing.html)
  - note that you might need to install a debugger like XDebug to run tests
- In the "src" directory, you have :
  - The controller directory which contains all the files used by the pages
  - The DataFixtures directory contains all the files to setup the database for the test
  - The Entity directory contains all the entity used in this project
  - The Manager directory contains all business methods that we can need about an Entity
  - The Repository directory contains all the files to do a request in the database
- In the "template" directory, you have all the files that are used to display things on the screen (the Twig files)
- In the "public" directory, you have the JS's script, the CSS's files and the different images
- In the "migrations" directory, you have all the files to build your database for the project
- In the "config" directory, you can go to the "package" directory to see the security.yaml
  - Into this file you can set the encoder and the role hierarchy
- In the root directory you'll find a .env file this will allow you to set up your project environement.
  - We strongly advise you to create .env.local in order to set up your local developpement environnement this is useful when you develop on localhost.
